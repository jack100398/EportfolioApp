<?php

namespace Database\Transfers;

use App\Models\Auth\User;
use App\Models\Exam\Exam;
use App\Models\Exam\ExamFolder;
use App\Models\Exam\ExamFolderAuthorization;
use App\Models\Exam\ExamQuestion;
use App\Models\Exam\ExamResult;
use App\Models\Exam\Pivot\ExamQuestionPivot;
use Exception;
use Illuminate\Support\Facades\DB;

class ExamTransfer
{
    private $folderIdList = [];

    private $examQuestionIdMapping = [];

    private $folderQuestionIdMapping = [];

    public function index()
    {
        DB::transaction(function () {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $this->createExam(); // 建立測驗與題目
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        });
        DB::transaction(function () {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $this->createFolder(); // 題庫
            $this->createExamFolderAuthorization(); // 題庫授權
            $this->createExamFolderPivot(); // 題庫與測驗關聯
            $this->createFolderQuesitons(); // 題庫題目
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        });
    }

    public function createExam()
    {
        $limit = 100;
        $index = 0;
        while (true) {
            $rawData = DB::connection('exam')
                ->table('common_exam')
                ->limit($limit)
                ->offset($limit * $index++)
                ->get();
            if ($rawData->count() === 0) {
                break;
            }
            echo "createExam....{$index}\n";
            foreach ($rawData as $row) {
                $this->examQuestionIdMapping = [];
                // 1. insert exam
                $exam = $this->insertExamData($row);
                // 2. insert exam questions
                $this->insertExamQuestions($exam, $row->exam_id);
                // 3. insert user results
                $this->insertUserResults($exam, $row->exam_id);
            }
        }
    }

    public function insertUserResults($exam, $rawExamId)
    {
        $rawData = DB::connection('exam')
            ->table('common_exam_user_info AS info')
            ->where('info.exam_id', $rawExamId)
            ->get();

        foreach ($rawData as $row) {
            $result = $this->generateResultArray($row, $rawExamId);
            ExamResult::forceCreate([
                'exam_id' => $exam->id,
                'user_id' => $row->user_id,
                'metadata' => $result,
                'score' => $row->score ?? 0,
                'is_marked' => $row->is_marking,
                'is_finished' => $row->is_finish,
                'start_time' => $row->start_time,
                'end_time' => $row->end_time,
                'source_ip' => $row->source_ip,
                'created_at' => $row->start_time,
                'updated_at' => $row->mtime,
                'deleted_at' => $row->is_delete ? $row->mtime : null,
            ]);
        }
    }

    public function insertExamQuestions($exam, $rawExamId)
    {
        $rawData = DB::connection('exam')
            ->table('common_exam_item')
            ->where('exam_id', $rawExamId)
            ->get();

        foreach ($rawData as $row) {
            $type = $this->convertQuestionType($row->type_id);
            $metadata = $this->getExamQuestionOptions($row, $rawExamId);
            $examQuestion = ExamQuestion::forceCreate([
                'folder_id' => null, // 實際上出題的題目都當作是修改過的題目
                'context' => $row->question_content,
                'metadata' => $metadata,
                'answer_detail' => $row->answer_detail ?? '',
                'type' => $type,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => $row->is_delete ? now() : null,
            ]);
            $this->examQuestionIdMapping[$rawExamId][$row->exam_item_id] = [];
            $this->examQuestionIdMapping[$rawExamId][$row->exam_item_id]['id'] = $examQuestion->id;
            $this->examQuestionIdMapping[$rawExamId][$row->exam_item_id]['type'] = $type;
            $this->createExamQuestionPivot($row, $exam->id, $examQuestion->id);
        }
    }

    public function insertExamData($row)
    {
        $randomParameter = $this->convertRandomParameter($row->random_parameter);

        $courseExam = $this->getCourseExam($row->exam_id);
        $exam = Exam::forceCreate([
            'id' => $row->exam_id,
            'title' => $row->exam_name,
            'description' => $row->description,
            'invigilator' => $row->invigilator,
            'start_time' => $row->start_time,
            'end_time' => $row->end_time,
            'is_answer_visible' => $row->stu_ans_visible,
            'scoring' => $row->scoring ?? 0,
            'passed_score' => $row->target ?? 0,
            'total_score' => $row->score ?? 0,
            'question_type' => $row->question_type,
            'random_parameter' => $randomParameter,
            'limit_times' => $row->limit_times ?? 0,
            'answer_time' => $row->answer_time,
            'created_by' => $row->user_id,
            'is_template' => $row->is_template,
            'course_id' => $courseExam === null ? null : $courseExam->course_id,
            'created_at' => $row->pub_time,
            'updated_at' => now(),
            'deleted_at' => $row->delete_date,
        ]);

        $this->examQuestionIdMapping[$row->exam_id]['id'] = $exam->id;

        return $exam;
    }

    private function getCourseExam($examId)
    {
        return DB::connection('raw')
        ->table('common_course_exam')
        ->where('exam_id', $examId)
        ->first('course_id');
    }

    public function convertRandomParameter($parameter)
    {
        if ($parameter === null) {
            return [];
        }
        $data = json_decode($parameter);
        $result = [];

        foreach ($data as $key => $value) {
            $newKey = $this->convertQuestionType($key);
            $result[$newKey] = $value;
        }

        ksort($result);

        return $result;
    }

    public function createFolder()
    {
        $limit = 1000;
        $index = 0;
        while (true) {
            $rawData = DB::connection('exam')
                ->table('common_exam_chapter')
                ->limit($limit)
                ->offset($limit * $index++)
                ->get();

            if ($rawData->count() === 0) {
                break;
            }
            echo "createFolder....{$index}\n";

            foreach ($rawData as $row) {
                if (in_array($row->chapter_id, $this->folderIdList)) {
                    continue;
                }

                if ($row->parent_chapter_id && ! in_array($row->parent_chapter_id, $this->folderIdList)) {
                    $this->createParentFolder($row->parent_chapter_id);
                }

                $folder = ExamFolder::forceCreate([
                    'id' => $row->chapter_id,
                    'name' => $row->chapter_name,
                    'parent_id' => $row->parent_chapter_id,
                    'type' => $row->type,
                    'created_by' => $row->user_id,
                    'created_at' => $row->create_time,
                    'updated_at' => now(),
                    'deleted_at' => $row->is_delete ? $row->update_time : null,
                ]);
                array_push($this->folderIdList, $folder->id);
            }
        }
    }

    public function createParentFolder($parent_id)
    {
        $raw = DB::connection('exam')
            ->table('common_exam_chapter')
            ->where('chapter_id', $parent_id)
            ->first();

        if ($raw->parent_chapter_id && ! in_array($raw->parent_chapter_id, $this->folderIdList)) {
            $this->createParentFolder($raw->parent_chapter_id);
        }

        $folder = ExamFolder::forceCreate([
            'id' => $raw->chapter_id,
            'name' => $raw->chapter_name,
            'parent_id' => $raw->parent_chapter_id,
            'type' => $raw->type,
            'created_by' => $raw->user_id,
            'created_at' => $raw->create_time,
            'updated_at' => now(),
            'deleted_at' => $raw->is_delete ? $raw->update_time : null,
        ]);

        array_push($this->folderIdList, $folder->id);

        return true;
    }

    public function createExamFolderPivot()
    {
        $limit = 1000;
        $index = 0;
        while (true) {
            $rawData = DB::connection('exam')
                ->table('common_exam')
                ->limit($limit)
                ->offset($limit * $index++)
                ->get(['exam_id', 'chapter_id']);

            if ($rawData->count() === 0) {
                break;
            }
            echo "createPivot....{$index}\n";

            foreach ($rawData as $row) {
                $exam = Exam::withTrashed()->find($row->exam_id);
                if ($exam === null) {
                    continue;
                }
                $chapter_ids = array_unique(explode(',', trim($row->chapter_id, '[]')));
                foreach ($chapter_ids as $chapter_id) {
                    if ($chapter_id > 0) {
                        $exam->examFolders()->attach($chapter_id);
                    }
                }
            }
        }
    }

    public function convertQuestionType($type_id)
    {
        switch ($type_id) {
            case 2:
                return ExamQuestion::TYPE_TRUEFALSE;
            case 3:
                return ExamQuestion::TYPE_CHOICE;
            case 5:
                return ExamQuestion::TYPE_ESSAY;
            case 7:
                return ExamQuestion::TYPE_FILL;
            default:
                throw new Exception('Unknown type.');
        }
    }

    public function getFolderQuestionOptions($data)
    {
        $type = $this->convertQuestionType($data->type_id);
        $options = array_unique(explode(',', trim($data->option_content, '[]')));
        $answer = array_unique(explode(',', trim($data->answer, '[]')));

        $optionData = DB::connection('exam')
            ->table('common_exam_question_option')
            ->whereIn('option_id', $options)
            ->get();

        return $this->createMetadata($optionData, $answer, $type, 0);
    }

    public function getExamQuestionOptions($data, $rawExamId)
    {
        $type = $this->convertQuestionType($data->type_id);
        $options = array_unique(explode(',', trim($data->option_content, '[]')));
        $answer = array_unique(explode(',', trim($data->answer, '[]')));

        $optionData = DB::connection('exam')
            ->table('common_exam_item_option')
            ->whereIn('option_id', $options)
            ->get();

        return $this->createMetadata($optionData, $answer, $type, $rawExamId);
    }

    public function createMetadata($optionData, $answerData, $type, $rawExamId)
    {
        switch ($type) {
            case ExamQuestion::TYPE_TRUEFALSE:
                return [
                    'option' => [
                        0 => 'False',
                        1 => 'True',
                    ],
                    'answer' => [($answerData[0] === 'O')],
                ];
            case ExamQuestion::TYPE_CHOICE:
                $option = [];
                $answer = [];
                $index = 0;
                foreach ($optionData as $value) {
                    $option[$index] = strip_tags($value->text);
                    $this->examQuestionIdMapping[$rawExamId][$value->option_id] = $index;
                    if (in_array($value->option_id, $answerData)) {
                        array_push($answer, $index);
                    }
                    $index++;
                }

                return [
                    'option' => $option,
                    'answer' => $answer,
                ];
            case ExamQuestion::TYPE_ESSAY:
                return [
                    'option' => [],
                    'answer' => [],
                ];
            case ExamQuestion::TYPE_FILL:
                $answer = [];
                foreach ($answerData as $value) {
                    array_push($answer, trim(strip_tags($value), '"'));
                }

                return [
                    'option' => [],
                    'answer' => $answer,
                ];
            default:
                throw new Exception('Unknown type.');
        }
    }

    public function createFolderQuesitons()
    {
        $limit = 1000;
        $index = 0;
        while (true) {
            $rawData = DB::connection('exam')
                ->table('common_exam_question')
                ->limit($limit)
                ->offset($limit * $index++)
                ->get();

            if ($rawData->count() === 0) {
                break;
            }
            echo "createFolderQuestion....{$index}\n";

            foreach ($rawData as $row) {
                $type = $this->convertQuestionType($row->type_id);
                $metadata = $this->getFolderQuestionOptions($row);
                $question = ExamQuestion::forceCreate([
                    'folder_id' => $row->chapter_id,
                    'context' => $row->question_content,
                    'metadata' => $metadata,
                    'answer_detail' => $row->answer_detail ?? '',
                    'type' => $type,
                    'created_at' => $row->create_time,
                    'updated_at' => $row->date_time,
                    'deleted_at' => $row->is_delete ? $row->date_time : null,
                ]);
                $this->folderQuestionIdMapping[$row->question_id] = $question->id;
            }
        }
    }

    public function createExamQuestionPivot($data, $rawExamId, $questionId)
    {
        return Exam::withTrashed()->find($rawExamId)
            ->examQuestions()
            ->attach($questionId, [
                'score' => $data->score,
                'sequence' => $data->sort,
            ]);
    }

    public function createExamQuestions()
    {
        $limit = 1000;
        $index = 0;
        while (true) {
            $rawData = DB::connection('exam')
                ->table('common_exam_item')
                ->limit($limit)
                ->offset($limit * $index)
                ->get();
            $index++;
            if ($rawData->count() === 0) {
                break;
            }

            foreach ($rawData as $row) {
                $type = $this->convertQuestionType($row->type_id);
                $metadata = $this->getExamQuestionOptions($row, $row->exam_id);
                $examQuestion = ExamQuestion::forceCreate([
                    'folder_id' => null, // 實際上出題的題目都當作是修改過的題目
                    'context' => $row->question_content,
                    'metadata' => $metadata,
                    'answer_detail' => $row->answer_detail ?? '',
                    'type' => $type,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deleted_at' => $row->is_delete ? now() : null,
                ]);

                $this->createExamQuestionPivot($row, $row->exam_id, $examQuestion->id);
            }
        }
    }

    public function createExamFolderAuthorization()
    {
        $limit = 1000;
        $index = 0;
        while (true) {
            $rawData = DB::connection('exam')
                ->table('common_exam_authorize')
                ->limit($limit)
                ->offset($limit * $index)
                ->get();
            $index++;
            if ($rawData->count() === 0) {
                break;
            }
            echo "createExamFolderUserAuth....{$index}\n";

            foreach ($rawData as $row) {
                if ($row->chapter_id === 0) {
                    continue;
                }
                $auth = ExamFolder::find($row->chapter_id)->authUsers()->attach($row->agent_user_id);
            }
        }

        $limit = 1000;
        $index = 0;
        while (true) {
            $rawData = DB::connection('exam')
                ->table('common_exam_authorize_group')
                ->limit($limit)
                ->offset($limit * $index)
                ->get();
            $index++;
            if ($rawData->count() === 0) {
                break;
            }
            echo "createExamFolderGroupAuth....{$index}\n";

            foreach ($rawData as $row) {
                if ($row->chapter_id === 0) {
                    continue;
                }
                // TODO: 單位授權
                // $auth = ExamFolder::find($row->chapter_id)->authGroups()->attach($row->group_id);
            }
        }
    }

    public function generateResultArray($info, $rawExamId)
    {// TODO: check if this part works properly
        $final = DB::connection('exam')
            ->table('common_exam_user_ans_final')
            ->where('exam_user_info_id', $info->exam_user_info_id)
            ->get();

        $result = [];
        foreach ($final as $row) {
            $question_id = $this->examQuestionIdMapping[$rawExamId][$row->exam_item_id]['id'];
            $pivotId = ExamQuestionPivot::where('question_id', $question_id)->first()->id;
            $type = $this->examQuestionIdMapping[$rawExamId][$row->exam_item_id]['type'];
            $result[$pivotId] = [
                'answer' => $this->getUserAnswer($row, $type, $rawExamId),
                'score' => $row->score,
            ];
        }

        return $result;
    }

    public function getUserAnswer($data, $type, $rawExamId)
    {
        $result = DB::connection('exam')
            ->table('common_exam_user_ans_result')
            ->where('user_ans_id', $data->user_ans_id)
            ->first();

        if ($result === null) {
            return [];
        }
        switch ($type) {
            case ExamQuestion::TYPE_TRUEFALSE:
                return [$result->ans_content === 'O'];
            case ExamQuestion::TYPE_CHOICE:
                return [$this->examQuestionIdMapping[$rawExamId][$result->ans_content]];
            case ExamQuestion::TYPE_FILL:
                return explode('</p>', rtrim(str_replace('<p>', '', $result->ans_content), '</p>'));
            case ExamQuestion::TYPE_ESSAY:
                return [$result->ans_content];
        }
    }
}
