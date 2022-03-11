<?php

namespace Tests\Unit\Services\Course;

use App\Models\Auth\User;
use App\Models\Course\AssessmentType;
use App\Models\Course\CourseStudentAssessment;
use App\Services\Course\CourseFormAuthService;
use App\Services\Course\CourseStudentAssessmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Throwable;

class CourseFormAuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private CourseFormAuthService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new CourseFormAuthService();
    }

    public function testCanGet()
    {
        $id = AssessmentType::factory()->create()->id;

        $subject = $this->service->getById($id);

        $this->assertTrue($subject instanceof AssessmentType);
    }
}
