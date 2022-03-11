<?php

namespace Database\Transfers\Form;

use App\Models\Form\Form;
use App\Models\Form\FormUnit;
use App\Models\Form\FormWriteRecord;
use App\Services\Form\Enum\FormWriteRecordFlagEnum;
use Database\Transfers\Form\FormQuestionTypeFactory\FormQuestionTypeFactory;
use Database\Transfers\Form\FormQuestionTypeFactory\Interfaces\IFormQuestionTypeFactory;
use DB;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class FormTransfer
{
    private IFormQuestionTypeFactory $formQuestionTypeFactory;

    public function __construct()
    {
        $this->formQuestionTypeFactory = new FormQuestionTypeFactory();
    }

    private function formTransferNewTable(object $oldForm): array
    {
        $form = [];
        $courseFormDefaultAssessment = $this->getCourseFormAssessment($oldForm->id);
        $formAssessment = $this->getFormAssessmentToJson($oldForm->id);
        $form['id'] = $oldForm->id;
        $form['origin_form_id'] = ($oldForm->child_form_id === 0) ? null : $oldForm->child_form_id;
        $form['name'] = $oldForm->name;
        $form['type'] = $oldForm->sub_type;
        $form['is_sharable'] = $oldForm->is_shared_form;
        $form['version'] = $oldForm->version;
        $form['is_enabled'] = $oldForm->enabled;
        $form['is_writable'] = (empty($oldForm->is_open_to_non_admin) || $oldForm->is_open_to_non_admin === 'null') ? null : $oldForm->is_open_to_non_admin;
        $form['questions'] = json_encode($this->formQuestionTypeTransfer($oldForm->id));
        $form['course_form_default_assessment'] = (isset($courseFormDefaultAssessment[0])) ? $courseFormDefaultAssessment[0] : 0;
        $form['form_default_workflow'] = (empty($formAssessment)) ? '[]' : $formAssessment;
        $form['deleted_at'] = ($oldForm->is_delete === 1) ? $oldForm->last_update : null;
        $form['created_at'] = $oldForm->create_time;
        $form['updated_at'] = $oldForm->last_update;

        return $form;
    }

    public function transfer()
    {
        $this->transferForm();
        $this->transferFormResult();
    }

    private function transferForm(): void
    {
        $index = 0;

        $start = microtime(true);

        while (true) {
            $forms = $this->getForm($index++);

            if ($forms->count() < 1) {
                break;
            }

            foreach ($forms as $form) {
                $this->storeForm($form);
            }
        }

        $time_elapsed_secs = microtime(true) - $start;
        echo $time_elapsed_secs;
    }

    private function transferFormResult()
    {
        $index = 0;

        $start = microtime(true);

        while (true) {
            $formResults = $this->getFormAssessmentResult($index++);

            if ($formResults->count() < 1) {
                break;
            }

            foreach ($formResults as $formResult) {
                $this->storeFormWriteRecord($formResult);
            }
        }

        $time_elapsed_secs = microtime(true) - $start;
        echo $time_elapsed_secs;
    }

    private function storeFormWriteRecord($formResult)
    {
        try {
            $this->formResultToNewTable($formResult);
            $this->formResultTempToNewTable($formResult);
        } catch (Exception $e) {
            echo 'error '.$formResult->survey_id."\n";
            var_dump($e->getMessage());
            Log::error($e->getMessage());
        }
    }

    private function formResultToNewTable($formResult): void
    {
        $result = $this->getFormQuestionResult($formResult->survey_id);
        if (count($result) > 0) {
            $questions = json_decode(Form::withTrashed()->find($formResult->form_id)->questions);
            $formWriteRecord['workflow_id'] = $formResult->ai_id;
            echo 'result ai_id: '.$formResult->ai_id."\n";
            $formWriteRecord['result'] = json_encode(
                $this->transferQuestionTypeResult(
                    $formResult->form_id,
                    $questions,
                    $result
                )
            );
            $formWriteRecord['flag'] = FormWriteRecordFlagEnum::RESULT;
            $formWriteRecord['user_id'] = $formResult->user_id;
            $formWriteRecord['created_at'] = $this->checkDateTime($formResult->sys_save_datetime);
            $formWriteRecord['updated_at'] = $this->checkDateTime($formResult->last_update);

            DB::transaction(function () use ($formWriteRecord) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                FormWriteRecord::forceCreate($formWriteRecord);
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            });
        }
    }

    private function checkDateTime($dateTime)
    {
        if (strtotime($dateTime) === false || strtotime($dateTime) <= 0) {
            return date('Y-m-d H:i:s');
        }

        return $dateTime;
    }

    private function transferQuestionTypeResult(int $formId, array $questions, $transferDatas): array
    {
        $questionTypeResult = [];
        $formResults = $this->setResultKey($transferDatas);

        foreach ($questions as $key => $question) {
            if ($question->type === 5) {
                $questionTypeResult[] = $this->getQuestionTypeToResult($formId, $key, $question->attributes->questions, $formResults);
            } else {
                //表單說明區塊
                $questionTypeResult[] = null;
            }
        }

        return $questionTypeResult;
    }

    private function getQuestionTypeToResult(int $formId, int $questionGroupKey, array $questionTypes, array $formResults)
    {
        $key = 1;
        $tempQuestionType = [];
        foreach ($questionTypes as $questionTypekey =>$questionType) {
            if ($questionType->type === 2) {
                //說明區塊直接跳過
                $tempQuestionType[] = null;
                continue;
            }

            if (isset($formResults[$questionGroupKey][$key])) {
                $formResult = $formResults[$questionGroupKey][$key];
                if ($questionType->type === 12) {
                    // 繪圖
                    $tempQuestionType[] = ['file_name'=>$formResult->sr_id.'.jpg', 'extension'=>'jpg'];
                } else {
                    $tempQuestionType[] = $formResult->ans_text;
                }
            } else {
                if ($questionType->type === 11) {
                    //題目檔案上傳
                    $questionTypeToUploadFile = $this->getQuestionTypeToUploadFile($formId, $questionGroupKey, $questionTypekey);
                    $uploadFile = $this->getUploadFile($questionTypeToUploadFile->ques_id);
                    $tempQuestionType[] = is_null($uploadFile) ? null : ['file_name'=>$uploadFile->file_name, 'extension'=>$uploadFile->ext];
                } else {
                    //簽名、說明區塊、圖片沒有存值
                    $tempQuestionType[] = null;
                }
            }
            $key++;
        }

        return $tempQuestionType;
    }

    private function setResultKey($transferData): array
    {
        $questionGroup = 0;
        $questionTypeResult = [];
        $questionType = [];
        foreach ($transferData as $result) {
            if ($questionGroup != $result->questionGroup) {
                if ($questionGroup != 0) {
                    $questionTypeResult[$questionGroup - 1] = $questionType;
                    $questionType = [];
                }
                $questionGroup = $result->questionGroup;
            }

            $questionType[$result->sequences] = $result;
        }
        $questionTypeResult[$questionGroup - 1] = $questionType;

        return $questionTypeResult;
    }

    private function formResultTempToNewTable($formResult): void
    {
        $result = $this->getFormQuestionResultTemp($formResult->survey_id);
        if (count($result) > 0) {
            echo 'temp ai_id: '.$formResult->ai_id."\n";
            $questions = json_decode(Form::withTrashed()->find($formResult->form_id)->questions);
            $formWriteRecord['workflow_id'] = $formResult->ai_id;
            $formWriteRecord['result'] = json_encode(
                $this->transferQuestionTypeResult(
                    $formResult->form_id,
                    $questions,
                    $this->getFormQuestionResultTemp($formResult->survey_id)
                )
            );
            $formWriteRecord['flag'] = FormWriteRecordFlagEnum::TEMP;
            $formWriteRecord['user_id'] = $formResult->user_id;
            $formWriteRecord['created_at'] = $this->checkDateTime($formResult->sys_save_datetime);
            $formWriteRecord['updated_at'] = $this->checkDateTime($formResult->last_update);

            DB::transaction(function () use ($formWriteRecord) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                FormWriteRecord::forceCreate($formWriteRecord);
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            });
        }
    }

    private function storeForm($form): void
    {
        try {
            $form = $this->formTransferNewTable($form);

            DB::transaction(function () use ($form) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                Form::forceCreate($form);
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            });

            $this->formGroupNewTable($form['id']);
            echo $form['id']."\n";
        } catch (Exception $e) {
            echo 'error '.$form['id']."\n";
            var_dump($e->getMessage());
            Log::error($e->getMessage());
        }
    }

    private function formGroupNewTable(int $formId): void
    {
        foreach ($this->getFormGroup($formId) as $oldFormGroup) {
            $formUnit = new FormUnit();
            $formUnit->form_id = $formId;
            $formUnit->unit_id = $oldFormGroup->group_id;

            DB::transaction(function () use ($formUnit) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                $formUnit->save();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            });
        }
    }

    private function formQuestionTypeTransfer(int $formId): array
    {
        $transferQuestion = [];
        foreach ($this->getFormQuestionGroup($formId) as $key => $oldQuestionGroup) {
            $oldQuestionGroup = (array) $oldQuestionGroup;
            if ($oldQuestionGroup['ques_type'] === 5) {
                $transferQuestion[$key] = $this->formQuestionTypeFactory->transferQuestionType(['question'=>$oldQuestionGroup, 'targe'=>[]]);

                foreach ($this->getFormQuestionType($formId, $oldQuestionGroup['ques_group']) as $questionKey => $oldQuestionType) {
                    $oldQuestionType = (array) $oldQuestionType;

                    if (in_array($oldQuestionType['ques_type'], [6,  7, 18])) {
                        $transferQuestion[$key]['attributes']['questions'][$questionKey] =
                        $this->formQuestionTypeFactory->transferQuestionType(
                            ['question'=>$oldQuestionType, 'targe'=> $this->getQuestionTypeIdentity($oldQuestionType['ques_id'])]
                        );

                        $transferQuestion[$key]['attributes']['questions'][$questionKey] =
                        $this->formQuestionTypeFactory->pushQuestion(
                            $transferQuestion[$key]['attributes']['questions'][$questionKey],
                            $this->checkQuestions($formId, $oldQuestionGroup['ques_group'], $oldQuestionType['ques_num'])
                        );
                    } else {
                        $transferQuestion[$key]['attributes']['questions'][] = $this->formQuestionTypeFactory->transferQuestionType(
                            ['question'=>$oldQuestionType, 'targe'=>$this->getQuestionTypeIdentity($oldQuestionType['ques_id'])]
                        );
                    }
                }
            } else {
                $transferQuestion[$key] = $this->formQuestionTypeFactory->transferQuestionType(
                    ['question'=>$oldQuestionGroup, 'targe'=> $this->getQuestionTypeIdentity($oldQuestionGroup['ques_id'])]
                );
            }
        }

        return $transferQuestion;
    }

    private function checkQuestions(int $formId, int $questionGroup, int $questionNum): array
    {
        $questions = [];
        foreach ($this->getFormQuestions($formId, $questionGroup, $questionNum) as $question) {
            $question = (array) $question;
            $questions[] = $this->formQuestionTypeFactory->transferQuestionType(['question' => $question,
                'targe'=> $this->getQuestionTypeIdentity($question['ques_id']), ]);
        }

        return $questions;
    }

    private function getQuestionTypeToUploadFile(int $formId, int $questionGroup, int $questionNum)
    {
        return DB::connection('raw')
            ->table('common_form_survey_ques')
            ->where([
                ['form_id', $formId],
                ['ques_type', 11],
                ['ques_group', $questionGroup + 1],
                ['ques_num', $questionNum + 1],
            ])
            ->first();
    }

    private function getForm(int $index): Collection
    {
        return DB::connection('raw')
                ->table('common_assessment_form')
                ->limit(100)
                ->offset(100 * $index++)
                ->orderBy('id', 'desc')
                ->get();
    }

    private function getQuestionTypeIdentity(int $ques_id): array
    {
        return DB::connection('raw')
        ->table('common_form_survey_ques_identity')
        ->select('role_id')
        ->where('ques_id', $ques_id)
        ->get()
        ->pluck('role_id')
        ->toArray();
    }

    private function getFormGroup(int $formId): Collection
    {
        return DB::connection('raw')
        ->table('common_assessment_form_group')
        ->where('form_id', $formId)
        ->get();
    }

    private function getFormQuestionGroup(int $formId): array
    {
        return DB::connection('raw')
        ->table('common_form_survey_ques')
        ->where('form_id', $formId)
        ->whereIn('ques_type', [5, 2])
        ->where('ques_num', 0)
        ->orderBy('ques_group')
        ->get()
        ->toArray();
    }

    private function getFormQuestionType(int $formId, int $questionGroup): array
    {
        return DB::connection('raw')
        ->table('common_form_survey_ques')
        ->where('form_id', $formId)
        ->where('ques_num', '!=', ' 0')
        ->where('ques_group', $questionGroup)
        ->whereNotIn('ques_type', [5, 3])
        ->orderBy('ques_id')
        ->orderBy('ques_num')
        ->get()
        ->toArray();
    }

    private function getFormQuestions(int $formId, int $questionGroup, int $questionNum): array
    {
        return DB::connection('raw')
        ->table('common_form_survey_ques')
        ->where('form_id', $formId)
        ->where('ques_num', $questionNum)
        ->where('ques_group', $questionGroup)
        ->where('ques_type', 3)
        ->orderBy('ques_id')
        ->orderBy('ques_num')
        ->get()
        ->toArray();
    }

    private function getCourseFormAssessment(int $formId): array
    {
        return DB::connection('raw')
        ->table('common_course_form_default_workflow')
        ->where('form_id', $formId)
        ->select('default_workflow_id')
        ->get()
        ->pluck('default_workflow_id')
        ->toArray();
    }

    private function getFormAssessmentToJson(int $formId): string
    {
        return DB::connection('raw')
        ->table('common_assessment_form_default_workflow')
        ->where('form_id', $formId)
        ->select('default_workflow_id')
        ->get()
        ->pluck('default_workflow_id')
        ->toJson();
    }

    private function getUploadFile(int $questionId): ?object
    {
        return DB::connection('raw')
            ->table('common_assessment_filepath')
            ->where('ques_id', $questionId)
            ->first();
    }

    private function getFormQuestionResult(int $surveyId): array
    {
        return DB::connection('raw')
        ->select("SELECT * FROM (SELECT
        SUBSTRING_INDEX(ques_path, '-', 1) AS questionGroup,
        SUBSTRING_INDEX(ques_path, '-', -1) AS sequences,
        common_form_survey_result.ans_text,
        common_form_survey_result.sr_id
        FROM common_form_survey_result
        where survey_id = ".$surveyId.'
        ) AS result ORDER BY questionGroup asc, sequences asc');
    }

    private function getFormQuestionResultTemp(int $surveyId): array
    {
        return DB::connection('raw')
        ->select("SELECT * FROM (SELECT
        SUBSTRING_INDEX(ques_path, '-', 1) AS questionGroup,
        SUBSTRING_INDEX(ques_path, '-', -1) AS sequences,
        common_form_survey_result_temp.ans_text,
        common_form_survey_result_temp.sr_id
        FROM common_form_survey_result_temp
        where is_delete = 0 and survey_id = ".$surveyId.'
        ) AS result ORDER BY questionGroup asc, sequences asc');
    }

    private function getFormAssessmentResult(int $index): Collection
    {
        return DB::connection('raw')
        ->table('common_form_survey')
        ->join(
            'common_assessment_result',
            'common_assessment_result.ai_id',
            '=',
            'common_form_survey.ai_id'
        )
        ->join('common_assessment_result_user', function ($join) {
            $join->on('common_assessment_result.ai_id', '=', 'common_assessment_result_user.ai_id')
                 ->where('common_assessment_result_user.ru_type', '=', 1);
        })
        ->limit(100)
        ->offset(100 * $index++)
        ->orderBy('survey_id', 'desc')
        ->get();
    }
}
