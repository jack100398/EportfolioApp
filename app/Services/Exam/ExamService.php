<?php

namespace App\Services\Exam;

use App\Models\Exam\Exam;
use App\Models\Exam\Pivot\ExamQuestionPivot;
use App\Models\Exam\QuestionMetadata\OptionFactory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class ExamService
{
    public function getManyByPagination(int $perPage): LengthAwarePaginator
    {
        return Exam::orderBy('created_at', 'DESC')
            ->paginate($perPage);
    }

    public function getById(int $id): ?Exam
    {
        return Exam::with(['examQuestions', 'examResults'])->find($id);
    }

    public function getQuestionsWithoutAnswerById(int $id): Collection
    {
        return Exam::findOrFail($id)
            ->examQuestions
            ->map(function ($question) {
                $option = OptionFactory::make($question->type, $question->metadata);

                return [
                    'context' => $question->context,
                    'option' => $option->showOptions(),
                    'type' => $question->type,
                ];
            }) ?? collect();
    }

    public function deleteById(int $id): bool
    {
        return Exam::findOrFail($id)->delete() === true;
    }

    public function create(array $data, int $createBy): int
    {
        $data['created_by'] = $createBy;

        return Exam::create($data)->id;
    }

    public function update(int $id, array $data): bool
    {
        return Exam::findOrFail($id)->update($data);
    }

    /**
     * 設定一份測驗的題目.
     *
     * @param int $examId
     * @param array $questions [$questionId => ['score' => int, 'sequence' => int]]
     *
     * @return bool
     */
    public function syncQuestionsIntoExam(int $examId, array $questions): bool
    {
        try {
            Exam::findOrFail($examId)
                ->examQuestions()
                ->sync($questions);

            return true;
        } catch (Throwable $th) {
            // 當使用者嘗試把不存在的題目同步到測驗時會報錯
            // TODO: 補上user資料，移除SQL
            // AuditTrail::create([
            //     'type' => 'C',
            //     'table_name' => 'exam_question_pivot',
            //     'changes' => 'no implementation',
            //     'table_id' => 0,
            //     'user_id' => 0,
            // ]);

            return false;
        }
    }

    public function changeExamQuestionOption(int $examId, int $questionId, array $metadata): bool
    {
        $pivot = $this->getQuestionPivot($examId, $questionId);

        $newQuestion = $pivot?->examQuestion?->replicate();

        if (! $newQuestion || ! $this->verifyMetadata($newQuestion->type, $metadata)) {
            return false;
        }

        DB::transaction(function () use ($pivot, $newQuestion, $metadata) {
            $newQuestion->metadata = $metadata;
            $newQuestion->save();
            $pivot->question_id = $newQuestion->id;
            $pivot->save();
        });

        return true;
    }

    public function changeQuestionScore(int $examId, int $questionId, int $score): bool
    {
        $pivot = $this->getQuestionPivot($examId, $questionId);
        if ($pivot === null) {
            return false;
        }

        $pivot->score = $score;
        $pivot->save();

        return $this->updateExamScore($examId);
    }

    public function getTemplateExams(): Collection
    {
        return Exam::whereIsTemplate(true)->get();
    }

    private function verifyMetadata(int $type, array $metadata): bool
    {
        $validator = OptionFactory::make($type, $metadata);

        return $validator->verify();
    }

    private function getQuestionPivot(int $examId, int $questionId): ?ExamQuestionPivot
    {
        return ExamQuestionPivot::where('exam_id', $examId)
            ->where('question_id', $questionId)
            ->first();
    }

    private function updateExamScore(int $examId): bool
    {
        $exam = Exam::findOrFail($examId);
        $score = (int) ExamQuestionPivot::where('exam_id', $examId)->sum('score');

        // 及格分數超過滿分
        if ($exam->passed_score > $score) {
            return false;
        }

        $exam->total_score = $score;

        return $exam->save();
    }
}
