<?php

namespace Database\Transfers;

use App\Models\Course\Survey\CourseSurvey;
use App\Models\Course\Survey\CourseSurveyRecord;
use App\Models\Course\Survey\Survey;
use App\Models\Course\Survey\SurveyQuestion;
use Illuminate\Support\Facades\DB;

class SurveyTransfer
{
    public function index()
    {
        DB::transaction(function () {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            echo "Survey Start\n";
            $this->surveyTransfer();
            echo "Survey END\n";
            echo "CourseSurvey Start\n";
            $this->courseSurveyTransfer();
            echo "CourseSurvey END\n";
            echo "SurveyQuestion START\n";
            $this->surveyQuestionTransfer();
            echo "SurveyQuestion END\n";
            echo "SurveyRecord START\n";
            $this->surveyRecordTransfer();
            echo "SurveyRecord END\n";
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        });
    }

    public function surveyTransfer()
    {
        $index = 0;
        DB::connection('raw')
        ->table('common_survey_base')
        ->orderBy('survey_id')
        ->chunk(100, function ($list) use (&$index) {
            $list->each(function ($survey) {
                $unit = DB::connection('raw')->table('common_survey_base_group')->where('survey_id', $survey->survey_id)->get();
                Survey::forceCreate([
                    'id' => $survey->survey_id,
                    'name' => $survey->name,
                    'version' => $survey->version,
                    'public' => $survey->public == 0 ? false : true,
                    'origin' => $survey->original_survey_id,
                    'unit_id' => $unit->count() > 0 ? $unit[0]->group_id : 0, //TODO:FIND CREATOR UNIT ID
                    'created_by' => $survey->user_id,
                    'updated_by' => $survey->delete_user_id == null ? $survey->user_id : $survey->delete_user_id,
                    'created_at' => $survey->create_time,
                    'updated_at' => $survey->update_time,
                    'deleted_at' => $survey->is_delete == '0' ? null : now(),
                ]);
            });

            echo $index.' to '.($index + 100).'Finished'."\n";

            $index += 100;
        });
    }

    public function courseSurveyTransfer()
    {
        $index = 0;
        DB::connection('raw')
        ->table('common_survey')
        ->orderBy('course_survey_id')
        ->chunk(100, function ($list) use (&$index) {
            $list->each(function ($courseSurvey) {
                CourseSurvey::forceCreate([
                    'id' => $courseSurvey->course_survey_id,
                    'survey_id' => $courseSurvey->base_survey_id,
                    'created_by' => $courseSurvey->user_id,
                    'start_at' => $courseSurvey->start_date,
                    'end_at' => $courseSurvey->end_date,
                    'created_at' => $courseSurvey->create_time,
                    'updated_at' => $courseSurvey->create_time,
                    'deleted_at' => $courseSurvey->is_delete == 0 ? null : now(),
                ]);
            });

            echo $index.' to '.($index + 100).'Finished'."\n";

            $index += 100;
        });
    }

    public function surveyQuestionTransfer()
    {
        $index = 0;
        DB::connection('raw')
        ->table('common_survey_base_question')
        ->orderBy('question_id')
        ->chunk(100, function ($list) use (&$index) {
            $list->each(function ($question) {
                $options = DB::connection('raw')->table('common_survey_base_option')->where('question_id', $question->question_id)->orderBy('option_id');

                $metadata = ['content'=>[], 'score'=> []];

                $options->each(function ($option) use (&$metadata) {
                    array_push($metadata['content'], $option->text);
                    array_push($metadata['score'], $option->score);
                });

                $type = ['radio' => 0, 'checkbox' => 1, 'text' => 2];

                SurveyQuestion::forceCreate([
                    'id' => $question->question_id,
                    'survey_id' => $question->survey_id,
                    'content' => $question->topic,
                    'sort' => $question->sort,
                    'type' => $type[$question->type],
                    'metadata' => $metadata,
                    'deleted_at' => $question->is_delete == 0 ? null : now(),
                ]);
            });

            echo $index.' to '.($index + 100).'Finished'."\n";

            $index += 100;
        });
    }

    public function surveyRecordTransfer()
    {
        $index = 0;
        DB::connection('raw')
        ->table('common_survey_record')
        ->orderBy('record_id')
        ->chunk(100, function ($list) use (&$index) {
            $list->each(function ($record) {
                $results = DB::connection('raw')->table('common_survey_result')->where('record_id', $record->record_id)->orderBy('result_id');

                $metadata = [];

                $results->each(function ($result) use (&$metadata) {
                    $questionOptions = DB::connection('raw')->table('common_survey_base_option')->where('question_id', $result->question_id)->orderBy('option_id')->pluck('option_id');

                    $options = array_flip($questionOptions->toArray());

                    $data = $result->comment == null ? $options[$result->option_id] : $result->comment;
                    array_push($metadata, $data);
                });

                CourseSurveyRecord::forceCreate([
                    'id' => $record->record_id,
                    'answered_by' => $record->user_id,
                    'course_survey_id' => $record->course_survey_id,
                    'role_type' => $record->record_type,
                    'metadata' => $metadata,
                    'created_at' => $record->record_time,
                    'updated_at' => $record->record_time,
                    'deleted_at' => $record->is_delete == 0 ? null : now(),
                ]);
            });

            echo $index.' to '.($index + 100).'Finished'."\n";

            $index += 100;
        });
    }

    public function test()
    {
        $question = SurveyQuestion::find(1);
        dd($question->metadata['content']);
    }
}
