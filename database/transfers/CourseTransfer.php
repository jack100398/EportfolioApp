<?php

namespace Database\Transfers;

use App\Models\Course\AssessmentType;
use App\Models\Course\Course;
use App\Models\Course\CourseAssessment;
use App\Models\Course\CourseMember;
use App\Models\Course\CoursePlace;
use App\Models\Course\CourseTarget;
use App\Models\Course\Credit;
use App\Models\Course\Feedback;
use Illuminate\Support\Facades\DB;

class CourseTransfer
{
    private $courseTeacher = 3;

    private $continueCreditChart = [];

    private $hospitalCreditChart = [];

    public function index()
    {
        DB::transaction(function () {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $this->CreditTransfer();
            $this->turningCourseTargetList();
            $this->turningAssessments();
            $this->turningCoursePlace();
            $this->turningCourseAndMember();
            $this->feedbackTransfer();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        });
    }

    public function turningCourseAndMember()
    {
        $index = 0;
        while (true) {
            echo($index * 100).' ~ '.(($index * 100) + 99)."\n";
            $courses = DB::connection('raw')->table('common_course')->limit(100)->offset(100 * $index++)->get();

            if ($courses->count() < 1) {
                break;
            }
            foreach ($courses as $course) {
                if ($course->year == null || $course->group_id == null) {
                    continue;
                }
                if ($course->start_time == '0000-00-00 00:00:00') {
                    $course->start_time = null;
                }
                if ($course->end_time == '0000-00-00 00:00:00') {
                    $course->end_time = null;
                }
                if ($course->signup_start_time == '0000-00-00 00:00:00') {
                    $course->signup_start_time = null;
                }
                if ($course->signup_end_time == '0000-00-00 00:00:00') {
                    $course->signup_end_time = null;
                }
                if ($course->course_form_send_time == '0000-00-00 00:00:00') {
                    $course->course_form_send_time = null;
                }
                Course::forceCreate(
                    [
                        'id'=>$course->course_id,
                        'year'=>$course->year,
                        'program_category_id'=>$course->category_course_id,
                        'default_category_id'=>$course->default_course_category_id,
                        'unit_id'=>$course->group_id,
                        'course_name'=>$course->course_name,
                        'course_remark'=>$course->course_remark,
                        'start_at'=>$course->start_time,
                        'end_at'=>$course->end_time,
                        'signup_start_at'=>$course->signup_start_time,
                        'signup_end_at'=>$course->signup_end_time,
                        'course_form_send_at'=>$course->course_form_send_time,
                        'open_signup_for_student'=>$course->open_signup_for_student,
                        'place'=>$course->place,
                        'course_mode'=>$course->course_mode,
                        'is_compulsory'=>$course->type,
                        'auto_update_students'=>$course->target_need_signup,
                        'created_by'=>$course->create_user_id,
                        'created_at'=>$course->create_time,
                        'updated_at'=>$course->update_time,
                        'updated_by'=>$course->update_user_id,
                        // 'initial_state'=>$course->initial_state,
                        'is_notified'=>$course->is_notify,
                        'metadata'=>$this->getCourseMetaData($course),
                        'deleted_at'=>$course->is_delete ? $course->update_time : null,
                    ]
                );
                $this->turningCourseTeacher($course);
                $this->turningCourseStudents($course->course_id);
                $this->turningCourseAssessments($course->course_id);
            }
            echo "Fin \n";
        }
        echo "COURSE - SUCCESSFUL \n";
    }

    private function getCourseMetaData($course): array
    {
        $metaData = [
            'people_limit' => $course->people_limit,
            'online_limit' => $course->online_limit,
            'other_teacher_name' => $course->other_teacher_name,
            'origin_course_id' => $course->origin_course_id,
            'continue_credit' => $course->continuing_edu_select_id != null ? $this->continueCreditChart[$course->continuing_edu_select_id] : null,
            'hospital_credit' => $this->hospitalCreditChart[$course->hospital_edu_type_id],
        ];

        $metaData = array_merge($metaData, $this->CourseTargetMetaData($course->course_id));
        $metaData = array_merge($metaData, $this->courseGroupLimitMetaData($course->course_id));

        return $metaData;
    }

    private function CourseTargetMetaData(int $courseId): array
    {
        $courseTargets = DB::connection('raw')->table('common_course_target')->where('course_id', $courseId)->get();
        $metaData = [];
        $newCourseTarget = new CourseTarget();
        foreach ($courseTargets as $target) {
            $courseTargetType = DB::connection('raw')->table('common_course_target_type')->where('target_id', $target->target_id)->first();
            if (! empty($courseTargetType)) {
                $newCourseTarget = CourseTarget::where('sort', $courseTargetType->sort)->first();
            }
        }

        $metaData = [
            'course_target' => $newCourseTarget->id,
        ];

        return $metaData;
    }

    private function courseGroupLimitMetaData(int $courseId): array
    {
        $courseGroupLimits = DB::connection('raw')->table('common_course_group_limit')->where('course_id', $courseId)->get();
        $limit = [];
        foreach ($courseGroupLimits as $groupLimit) {
            $limit[$groupLimit->group_id] = $groupLimit->limit_num;
        }
        $metaData = [
            'unit_limit' => $limit,
        ];

        return count($limit) > 0 ? $metaData : [];
    }

    public function turningCourseTeacher($course): void
    {
        $courseTeachers = DB::connection('raw')->table('common_course_teacher')->where('course_id', $course->course_id)->get();
        foreach ($courseTeachers as $teacher) {
            $role = $teacher->teacher_user_id == $course->teacher_user_id ? $this->courseTeacher : $this->getTeacherMemberRole($teacher->teaching_mode, $teacher->system_auto);
            CourseMember::forceCreate(
                [
                    'course_id' => $teacher->course_id,
                    'user_id' => $teacher->teacher_user_id,
                    'role' => $role,
                    'is_online_course' => 0,
                    'updated_by' => $course->create_user_id,
                    'state' => $teacher->state,
                ]
            );
        }
    }

    private function getTeacherMemberRole(int $teachingMode, int $systemAuto): int
    {
        //偕同教師 1  +     1     +    0      =  2
        //課程教師 1  +     1     +    2      =  4
        return 1 + $teachingMode + $systemAuto * 2;
    }

    public function turningCourseStudents($courseId)
    {
        $courseStudents = DB::connection('raw')->table('common_course_student')->where('course_id', $courseId)->get();

        foreach ($courseStudents as $student) {
            CourseMember::forceCreate(
                [
                    'course_id' => $student->course_id,
                    'user_id' => $student->student_user_id,
                    'role' => 1,
                    'is_online_course' => $student->signup_type,
                    'joined_at'=>$student->signup_time,
                    'updated_by' => $student->update_user_id,
                    'updated_at' => $student->update_time,
                    'state' => $student->state,
                ]
            );
        }
    }

    public function turningCourseTargetList()
    {
        $courseTargetTypes = DB::connection('raw')->table('common_course_target_type')->get();

        foreach ($courseTargetTypes as $targetType) {
            $courseTarget = CourseTarget::factory()->make();
            $courseTarget->target_name = $targetType->target_name;
            $courseTarget->sort = $targetType->sort;
            $courseTarget->viewable = $targetType->display;

            $courseTarget->save();
        }

        echo "COURSE TARGET TYPE - SUCCESSFUL \n";
    }

    public function turningCoursePlace()
    {
        $coursePlaces = DB::connection('raw')->table('common_course_place')->whereNull('parent_place_id')->get();
        $this->insertCoursePlace($coursePlaces);
        $coursePlaces = DB::connection('raw')->table('common_course_place')->whereNotNull('parent_place_id')->get();
        $this->insertCoursePlace($coursePlaces);

        echo "COURSE PLACE - SUCCESSFUL \n";
    }

    private function insertCoursePlace($coursePlaces)
    {
        foreach ($coursePlaces as $place) {
            if ($place->parent_place_id != null && ! CoursePlace::where('id', $place->parent_place_id)->exists()) {
                continue;
            }

            CoursePlace::forceCreate(
                [
                    'id'=>$place->place_id,
                    'parent_id' => $place->parent_place_id,
                    'name' => $place->place_name,
                ]
            );
        }
    }

    public function turningAssessments()
    {
        $assessments = DB::connection('raw')->table('common_course_assessment_sub_type')->get();
        foreach ($assessments as $assessment) {
            $arr = explode('_', $assessment->type_id);

            AssessmentType::forceCreate(
                [
                    'id'=>$assessment->sub_assessment_id,
                    'type'=>$assessment->assessment_id,
                    'assessment_name'=>$assessment->assessment_name,
                    'unit_id'=>$assessment->group_id,
                    'source'=>$assessment->type_id === null ? null : $arr[1],
                    'deleted_at'=>$assessment->display ? Now() : null,
                ]
            );
        }
        echo "SUB ASSESSMENT - SUCCESSFUL \n";
    }

    public function turningCourseAssessments(int $courseId)
    {
        $courseAssessments = DB::connection('raw')->table('common_course_assessment')->where('course_id', $courseId)->get();
        foreach ($courseAssessments as $assessment) {
            CourseAssessment::forceCreate(
                [
                    'id'=>$assessment->id,
                    'course_id'=>$assessment->course_id,
                    'assessment_id'=>$assessment->sub_assessment_id,
                    'data'=>$assessment->data,
                ]
            );
        }
    }

    public function CreditTransfer()
    {
        $this->turnContinueCredit();
        $this->turnHospitalCredit();
    }

    private function turnHospitalCredit()
    {
        $hospitalCredits = DB::connection('raw')->table('hospital_edu_type')->get();
        foreach ($hospitalCredits as $credit) {
            $eduYear = DB::connection('raw')->table('common_edu_type_year')->find($credit->year);
            $hospitalCredit = Credit::forceCreate([
                'year' => $eduYear === null ? $credit->year : $eduYear->year,
                'sort' => $credit->sort,
                'parent_id' => null,
                'credit_name' => $credit->hospital_edu_type_name,
                'credit_type' => 1,
                'training_time' => $this->createCreditTrainingTime($credit->hospital_edu_type_id, $credit->year),
            ]);
            $this->hospitalCreditChart[$credit->hospital_edu_type_id] = $hospitalCredit->id;
        }
        echo "HospitalCredit - SUCCESSFUL \n";
    }

    private function createCreditTrainingTime(int $originId, int $yearId): array
    {
        if ($yearId === 0) {
            return [];
        }
        $res = [];
        DB::connection('raw')
            ->table('common_edu_type_staff_category')
            ->where('edu_type_id', $originId)
            ->get(['sc_id', 'hour_number'])
            ->each(function ($staffCategory) use (&$res) {
                $res[$staffCategory->sc_id] = $staffCategory->hour_number;
            });

        return $res;
    }

    private function turnContinueCredit()
    {
        $continueCreditTypes = DB::connection('raw')->table('continuing_edu_type')->get();

        foreach ($continueCreditTypes as $type) {
            $credit = Credit::forceCreate([
                'year' => 0,
                'sort' => 0,
                'parent_id' => null,
                'credit_name' => $type->continuing_edu_type_name,
                'credit_type' => 2,
            ]);
            $continueCreditSelects = DB::connection('raw')->table('continuing_edu_select')->where('continuing_edu_type_id', $type->continuing_edu_type_id)->get();
            foreach ($continueCreditSelects as $select) {
                $continueSelect = Credit::forceCreate([
                    'year' => 0,
                    'sort' => 0,
                    'parent_id' => $credit->id,
                    'credit_name' => $select->continuing_edu_select_name,
                    'credit_type' => 2,
                ]);
                $this->continueCreditChart[$select->continuing_edu_select_id] = $continueSelect->id;
            }
        }
        echo "Continue Credit - SUCCESSFUL \n";
    }

    public function feedbackTransfer()
    {
        $index = 0;
        DB::connection('raw')
        ->table('user_teacher_feedback')
        ->orderBy('id')
        ->chunk(100, function ($feedbacks) use (&$index) {
            $feedbacks->each(function ($feedback) {
                Feedback::forceCreate([
                    'id' => $feedback->id,
                    'comment' => $feedback->comment,
                    'public' => $feedback->type,
                    'usage' => $feedback->usage,
                    'created_by' => $feedback->user_id,
                    'deleted_at' => $feedback->is_delete == 1 ? now() : null,
                ]);
            });

            echo $index.' to '.($index + 100).'Finished'."\n";

            $index += 100;
        });
    }
}
