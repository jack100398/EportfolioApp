<?php

namespace Database\Transfers;

use App\Models\Workflow\ScheduleSendWorkflowForm;
use App\Services\Workflow\Enum\WorkflowTypeEnum;
use DB;
use Illuminate\Support\Collection;

class ScheduleCourseFormTransfer
{
    public function transfer()
    {
        $index = 0;

        $start = microtime(true);

        while (true) {
            $queues = $this->getScheduleCourseFormSend($index++);

            if ($queues->count() < 1) {
                break;
            }

            foreach ($queues as $queue) {
                $this->storeScheduleSendWorkflowForm($queue);
            }
        }

        $time_elapsed_secs = microtime(true) - $start;
        echo $time_elapsed_secs;
    }

    private function storeScheduleSendWorkflowForm(object $queue)
    {
        $assessmentType = $this->getCommonCourseAssessmentSubType($queue->sub_assessment_id);
        if (is_null($assessmentType)) {
            return;
        }

        $assessmentInformation = $this->getCommonCourseCormAssessmentCourse($queue->course_id, explode('form_', $assessmentType->type_id)[1]);
        $scheduleSendWorkflowForm = new ScheduleSendWorkflowForm();
        $scheduleSendWorkflowForm->key_id = 1;
        $scheduleSendWorkflowForm->title = $assessmentInformation->name;
        $scheduleSendWorkflowForm->unit_id = $assessmentType->group_id;
        $scheduleSendWorkflowForm->type = WorkflowTypeEnum::COURSE;
        $scheduleSendWorkflowForm->start_at = $assessmentInformation->start_date;
        $scheduleSendWorkflowForm->end_at = $assessmentInformation->end_date;
        $scheduleSendWorkflowForm->create_at = $queue->create_user_id;
        $scheduleSendWorkflowForm->student_id = $queue->student_user_id;
        try {
            DB::transaction(function () use ($scheduleSendWorkflowForm) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                $scheduleSendWorkflowForm->save();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            });
            // echo $scheduleSendWorkflowForm->id."\n";
        } catch (\Throwable $th) {
            //throw $th;
            echo $assessmentInformation->id."\n";
        }
    }

    private function getScheduleCourseFormSend(int $index): Collection
    {
        return DB::connection('raw')
            ->table('schedule_course_form_send')
            ->where('is_delete', false)
            ->where('state', 1)
            ->limit(100)
            ->offset(100 * $index++)
            ->orderBy('id', 'asc')
            ->get();
    }

    private function getCommonCourseAssessmentSubType(int $subAssessmentId)
    {
        return DB::connection('raw')
            ->table('common_course_assessment_sub_type')
            ->where('sub_assessment_id', $subAssessmentId)
            ->first();
    }

    private function getCommonCourseCormAssessmentCourse(int $courseId, int $typeId)
    {
        return DB::connection('raw')
            ->table('common_course_form_assessment_course')
            ->join(
                'common_assessment_information',
                'common_assessment_information.id',
                '=',
                'common_course_form_assessment_course.information_id'
            )
            ->where('course_id', $courseId)
            ->where('type_id', $typeId)
            ->first();
    }
}
