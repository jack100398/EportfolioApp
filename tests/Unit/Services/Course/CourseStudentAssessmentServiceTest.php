<?php

namespace Tests\Unit\Services\Course;

use App\Models\Auth\User;
use App\Models\Course\Course;
use App\Models\Course\CourseStudentAssessment;
use App\Services\Course\CourseStudentAssessmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Throwable;

class CourseStudentAssessmentServiceTest extends TestCase
{
    use RefreshDatabase;

    private CourseStudentAssessmentService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new CourseStudentAssessmentService();
    }

    public function testCanGet()
    {
        $studentAssessment = CourseStudentAssessment::factory()->create();

        $subject = $this->service->getById($studentAssessment->id);

        $this->assertTrue($subject instanceof CourseStudentAssessment);
    }

    public function testCanUpdate()
    {
        $studentAssessment = CourseStudentAssessment::factory()->create();

        $newStudentId = User::factory()->create()->id;
        $this->service->update($studentAssessment->id, ['student_id' => $newStudentId]);

        $idChecker = $this->service->getById($studentAssessment->id)->student_id;

        $this->assertSame($idChecker, $newStudentId);
    }

    public function testCanCreate()
    {
        $studentAssessment = CourseStudentAssessment::factory()->make();

        $id = $this->service->create($studentAssessment->toArray());

        $subject = $this->service->getById($id);

        $this->assertTrue($subject instanceof CourseStudentAssessment);
    }

    public function testCanDelete()
    {
        $studentAssessment = CourseStudentAssessment::factory()->create();

        $this->assertTrue($this->service->delete($studentAssessment->id));
    }

    public function testCanGetByCourseId()
    {
        $courseId = Course::factory()->create()->id;

        CourseStudentAssessment::factory(5)->create(['course_id' => $courseId]);

        $this->assertTrue($this->service->getByCourseId($courseId)->count() === 5);
    }

    public function testCanGetByStudentId()
    {
        $studentId = User::factory()->create()->id;

        CourseStudentAssessment::factory(5)->create(['student_id' => $studentId]);

        $this->assertTrue($this->service->getByStudentId($studentId)->count() === 5);
    }
}
