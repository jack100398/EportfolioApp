<?php

namespace App\Services\Exam;

use App\Models\Exam\ExamQuestion;
use App\Models\Exam\ExamResult;
use App\Models\Exam\Pivot\ExamQuestionPivot;
use App\Models\Exam\QuestionMetadata\IOption;
use App\Models\Exam\QuestionMetadata\OptionFactory;
use Illuminate\Database\Eloquent\Collection;

class ExamResultService
{
    public function getById(int $id): ?ExamResult
    {
        return ExamResult::find($id);
    }

    public function deleteById(int $id): bool
    {
        return ExamResult::findOrFail($id)->delete() === true;
    }

    public function create(array $data): int
    {
        $data['metadata'] = $this->makeAnswerMetadata($data['exam_id']);

        return ExamResult::create($data)->id;
    }

    public function update(int $id, array $data): bool
    {
        return ExamResult::findOrFail($id)->update($data);
    }

    /**
     * 作答自動評分，如果有無法評分的題型還是需要手動評分.
     *
     * @param int $id
     *
     * @return bool
     */
    public function autoMarkingResult(int $id): bool
    {
        $result = ExamResult::findOrFail($id);
        if (! $result->is_finished) {
            return false;
        }

        return $this->markingAnswer($result);
    }

    /**
     * 老師直接評分.
     *
     * @param int $id Result Id
     * @param array $scores [ pivotId => score = '' ]
     *
     * @return bool
     */
    public function manualMarkingResult(int $id, array $scores): bool
    {
        $result = ExamResult::findOrFail($id);

        if (! $result->is_finished) {
            return false;
        }

        $scores = collect($scores);
        $scores->each(function (int $score, int $pivotId) use ($result) {
            $result = $this->updateScore($result, $pivotId, $score);
        });

        $result->score = $scores->sum();

        return $result->save();
    }

    public function makeAnswerMetadata(int $examId): array
    {
        $answers = ExamQuestionPivot::where('exam_id', $examId)
            ->get()
            ->keyBy('id')
            ->map(fn () => ['answer' => [], 'score' => 0]);

        return $answers->all();
    }

    /**
     * 作答結果評分並儲存.
     *
     * @param ExamResult $result
     *
     * @return bool
     */
    private function markingAnswer(ExamResult $result): bool
    {
        $questions = $result->exam?->examQuestions;
        if ($questions === null) {
            return false;
        }

        $result->metadata = $this->calculateScoreForEachRecord($result, $questions);
        $result->score = $this->getTotalScore($result);
        $result->is_marked = $this->checkQuestionsAutoMarkable($questions);

        return $result->save();
    }

    private function calculateScoreForEachRecord(ExamResult $result, Collection $questions): array
    {
        return collect($result->metadata)
            ->map(function ($record, $pivotId) use ($questions) {
                $question = $questions->where('pivot.id', $pivotId)->first();
                $option = $this->makeOption($question);

                if ($option->canMarkScore() && $option->isCorrect($record['answer'])) {
                    $record['score'] = $question->pivot->score;
                }

                return $record;
            })->toArray();
    }

    private function getTotalScore(ExamResult $result): int
    {
        return collect($result->metadata)->sum(fn ($record) => $record['score']);
    }

    private function checkQuestionsAutoMarkable(Collection $questions): bool
    {
        return $questions
            ->filter(fn ($q) => ! $this->makeOption($q)->canMarkScore())
            ->count() === 0;
    }

    private function updateScore(ExamResult $result, int $pivotId, int $score): ExamResult
    {
        $metadata = $result->metadata;
        $metadata[$pivotId]['score'] = $score;
        $result->metadata = $metadata;

        return $result;
    }

    private function makeOption(ExamQuestion $question): IOption
    {
        return OptionFactory::make($question->type, $question->metadata);
    }
}
