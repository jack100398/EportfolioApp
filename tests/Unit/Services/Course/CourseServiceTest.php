<?php

namespace Tests\Unit\Services\Course;

use App\Http\Requests\Course\CreateCourseRequest;
use App\Http\Requests\Course\SearchCourseRequest;
use App\Models\Auth\User;
use App\Models\Course\AssessmentType;
use App\Models\Course\Course;
use App\Models\Course\CourseAssessment;
use App\Models\Course\CourseHistory;
use App\Models\Course\CourseMember;
use App\Models\Course\CourseTarget;
use App\Models\Course\Credit;
use App\Models\TrainingProgram\TrainingProgramCategory;
use App\Models\Unit;
use App\Services\Course\CourseMemberService;
use App\Services\Course\CourseService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertTrue;
use Tests\TestCase;
use Throwable;

class CourseServiceTest extends TestCase
{
    use RefreshDatabase;

    private CourseService $service;

    private CourseMemberService $memberService;

    public function __construct()
    {
        parent::__construct();
        $this->service = new CourseService();
        $this->memberService = new CourseMemberService();
    }

    public function testCanGet()
    {
        $course = Course::factory()->create();

        $subject = $this->service->getCourseById($course->id);

        $this->assertTrue($subject instanceof Course);
    }

    public function testCanInsert()
    {
        $data = Course::factory()->make()->toArray();

        $courseId = $this->service->create($data);

        $this->assertTrue($courseId > 0);
    }

    public function testCanUpdate()
    {
        $course = Course::factory()->create(['course_name' => 'old']);

        $data = ['course_name' => 'new'];
        $this->service->update($course->id, $data);

        $courseName = Course::find($course->id)->course_name;

        $this->assertSame($courseName, 'new');
    }

    public function testCanDelete()
    {
        $course = Course::factory()->create();
        $this->assertTrue($course->id > 0);

        $this->service->delete($course->id);
        $course = Course::find($course->id);
        $this->assertNull($course);
    }

    public function testCanReStore()
    {
        $course = Course::factory()->create();
        $courseId = $course->id;
        $this->service->delete($course->id);
        $course = Course::find($course->id);
        $this->assertNull($course);

        $this->service->restoreCourseById($courseId);
        $course = Course::find($courseId);
        $this->assertTrue($course instanceof Course);
    }

    public function testRestoreFail()
    {
        $this->expectException(ModelNotFoundException::class);

        $course = Course::factory()->create();
        $courseId = $course->id;
        $this->service->restoreCourseById($courseId + 1);
    }

    public function testSearchByRequest()
    {
        $course = $this->createCourseByRequest();

        $hintCourseIds = $this->searchCourseByRequest($course);

        $this->assertTrue($hintCourseIds->contains($course->id));
    }

    public function testTrimCourseByCredit()
    {
        $course = $this->createCourseByRequest();
        $courseIds = $this->searchCourseByRequest($course);

        $hintCourseIds = $this->service->trimCourseListByCredit($courseIds, [$course->metadata['continuing_credit']]);
        $this->assertTrue($hintCourseIds->contains($course->id));
        $hintCourseIds = $this->service->trimCourseListByCredit($courseIds, [999]);
        $this->assertFalse($hintCourseIds->contains($course->id));
        $hintCourseIds = $this->service->trimCourseListByCredit($courseIds, [$course->metadata['hospital_credit']]);
        $this->assertTrue($hintCourseIds->contains($course->id));
        $hintCourseIds = $this->service->trimCourseListByCredit($courseIds, [999, $course->metadata['hospital_credit']]);
        $this->assertTrue($hintCourseIds->contains($course->id));
    }

    public function testCanCloneCourses()
    {
        $courses = Course::factory(3)->create();
        $map = $courses->mapWithKeys(
            fn ($c) => [$c->program_category_id => TrainingProgramCategory::factory()->create()->id]
        );

        $result = $this->service->cloneCourses($courses, $map->toArray());

        $result->each(function ($newCourse) use ($courses, $map) {
            $oldCourse = $courses
                ->where('program_category_id', $map->flip()[$newCourse->program_category_id])
                ->first();

            $this->assertNotSame($newCourse->id, $oldCourse->id);
            $this->assertSame($newCourse->course_name, $oldCourse->course_name);
        });
    }

    public function testCanShareCourseToProgramCategory()
    {
        $course = Course::factory()->create();
        $programCategory = TrainingProgramCategory::factory()->create();

        $this->service->shareCourseToCategory($course->id, $programCategory->id);

        $result = Course::find($course->id)->courseShares->first()->id;
        $this->assertSame($programCategory->id, $result);
    }

    private function searchCourseByRequest($course): Collection
    {
        $request = new SearchCourseRequest([
            'course_mode' => [$course->course_mode],
            'year' => $course->year,
            'unit_id' => $course->unit_id,
        ]);

        return $this->service->getCoursesByRequest($request->all());
    }

    private function createCourseByRequest(): Course
    {
        $request = $this->getCreateCourseRequest();
        $courseId = $this->service->create($request->all());
        $this->memberService->createCourseTeacher($courseId, $request->teachers, $request->created_by);

        return Course::find($courseId);
    }

    private function getCreateCourseRequest(): CreateCourseRequest
    {
        $request = new CreateCourseRequest([
            'year' => 111,
            'course_name' => 'testing',
            'program_category_id' => TrainingProgramCategory::factory()->create()->id,
            'unit_id' => Unit::factory()->create()->id,
            'course_remark' => 'Just A Test',
            'teachers' => [['id'=>User::factory()->create()->id, 'role'=> 3], ['id'=>User::factory()->create()->id, 'role'=> 2]],
            'assessment' => [AssessmentType::factory()->create()->id => '2'],
            'place' => 'Cloud.com',
            'start_at' => '2021-09-20 06:00:00',
            'end_at' => '2021-09-20 08:00:00',
            'signup_start_at' => '2021-09-16 06:00:00',
            'signup_end_at' => '2021-09-19 12:00:00',
            'course_form_send_time' => '2021-09-20 06:00:00',
            'auto_update_students' => true,
            'open_signup_for_student' => true,
            'is_compulsory' => false,
            'course_mode' => 5,
            'created_by' => User::factory()->create()->id,
            'updated_by' => User::factory()->create()->id,
            'is_notified' => false,
            'course_target' => CourseTarget::factory()->create()->id,
            'people_limit' => 10,
            'combine_course' => Course::factory()->create()->id,
            'other_teacher' => 'Jan',
            'continuing_credit' => Credit::factory()->create()->id,
            'hospital_credit' => Credit::factory()->create()->id,
            'students' => [User::factory()->create()->id => false, User::factory()->create()->id => false, User::factory()->create()->id => true],
        ]);
        $metadataColumn = ['course_target', 'people_limit', 'combine_course', 'other_teacher', 'continuing_credit', 'hospital_credit'];
        $request['metadata'] = $this->service->createCourseMetaData($request->only($metadataColumn));

        return $request;
    }
}
