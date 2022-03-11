<?php

namespace Tests\Feature\Course;

use App\Models\Auth\User;
use App\Models\Course\AssessmentType;
use App\Models\Course\Course;
use App\Models\Course\CourseAssessment;
use App\Models\Course\CourseTarget;
use App\Models\Course\Credit;
use App\Models\TrainingProgram\TrainingProgramCategory;
use App\Models\Unit;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CourseControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
    }

    public function testCanShowIndex()
    {
        $response = $this->get('/api/course?size=10');

        $response->assertOk();
    }

    public function testCreateCourse()
    {
        $this->createForeignData();
        $response = $this->post('/api/course', [
            'year' => 111,
            'course_name' => 'testing',
            'program_category_id' => TrainingProgramCategory::factory()->create()->id,
            'unit_id' => Unit::factory()->create()->id,
            'course_remark' => 'Just A Test',
            'teachers' => [['id'=>User::factory()->create()->id, 'role'=> 3], ['id'=>User::factory()->create()->id, 'role'=> 2]],
            'assessment' => [AssessmentType::first()->id => '2'],
            'place' => 'Cloud.com',
            'start_at' => '2021-09-20 06:00:00',
            'end_at' => '2021-09-20 08:00:00',
            'signup_start_at' => '2021-09-16 06:00:00',
            'signup_end_at' => '2021-09-19 12:00:00',
            'course_form_send_at' => '2021-09-20 06:00:00',
            'auto_update_students' => true,
            'open_signup_for_student' => true,
            'is_compulsory' => false,
            'course_mode' => 5,
            'is_notified' => false,
            'course_target' => CourseTarget::first()->id,
            'people_limit' => 10,
            'combine_course' => Course::first()->id,
            'other_teacher' => 'Jan',
            'continuing_credit' => Credit::first()->id,
            'hospital_credit' => 2,
            'students' => [User::factory()->create()->id => false, User::factory()->create()->id => false, User::factory()->create()->id => true],
            'metadata' => ['course_target' => CourseTarget::factory()->create()->id],
            'overdue_type' => 1,
            'overdue_description' => 'string',
        ]);

        $response->assertCreated();
    }

    private function createForeignData(): void
    {
        AssessmentType::factory(10)->create();
        CourseTarget::factory(4)->create();
        Credit::factory(10)->create();
        Course::factory(10)->create();
    }

    public function testUpdateCourse()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $originCourseId = Course::factory()->create()->id;

        $course = Course::factory()->make();

        $response = $this->put('/api/course/'.$originCourseId, [
            'year' => 222222,
            'course_name' => '測試',
            'program_category_id' => $course->program_category_id,
            'unit_id' => $course->unit_id,
            'course_remark' => $course->course_remark,
            'auto_update_students' => $course->auto_update_students,
            'open_signup_for_student' =>  $course->open_signup_for_student,
            'metadata' =>  $course->metadata,
            'is_compulsory' =>  $course->is_compulsory,
            'course_mode' =>  $course->course_mode,
            'is_notified' =>  $course->is_notified,
            'overdue_type' => 1,
            'overdue_description' => 'string',
        ]);
        $response->assertNoContent();
    }

    public function testShowCourse()
    {
        Course::factory(10)->create();

        $response = $this->get('/api/course/'.Course::first()->id);
        $response->assertOk();
    }

    public function testDeleteCourse()
    {
        Course::factory(10)->create();

        $response = $this->delete('/api/course/'.Course::first()->id);
        $response->assertNoContent();
    }

    public function testSearchCourseById()
    {
        $course = Course::factory()->create(['metadata' => ['hospital_credit' => 1, 'continuing_credit' => 2]]);

        CourseAssessment::factory()->create(['course_id' => $course->id]);

        $response = $this->post('/api/course/search', [
            'year' => $course->year,
            'course_mode' => [$course->course_mode],
            'unit_id' => $course->unit_id,
            'credit' => [1, 2],
            'assessment_id' => [1],
            'searchContent' => (string) $course->id,
        ]);

        $response->assertOk();
    }

    public function testSearchCourseByName()
    {
        $course = Course::factory()->create();

        CourseAssessment::factory()->create(['course_id' => $course->id]);

        $response = $this->post('/api/course/search', [
            'year' => $course->year,
            'course_mode' => [$course->course_mode],
            'unit_id' => $course->unit_id,
            'credit' => [1, 2],
            'assessment_id' => [1],
            'searchContent' => $course->course_name,
        ]);

        $response->assertOk();
    }

    public function testCanShareCourseToProgramCategory()
    {
        $course = Course::factory()->create();
        $programCategory = TrainingProgramCategory::factory()->create();

        $response = $this->post("/api/course/$course->id/share/$programCategory->id");

        $response->assertNoContent();
    }
}
