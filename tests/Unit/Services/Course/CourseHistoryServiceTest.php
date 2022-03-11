<?php

namespace Tests\Unit\Services\Course;

use App\Models\Course\AssessmentType;
use App\Models\Course\Course;
use App\Models\Course\CourseHistory;
use App\Services\Course\CourseHistoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Throwable;

class CourseHistoryServiceTest extends TestCase
{
    use RefreshDatabase;

    private CourseHistoryService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new CourseHistoryService();
    }

    public function testCanGet()
    {
        $id = CourseHistory::factory()->create(['course_id' => Course::factory()->create()->id])->id;

        $subject = $this->service->getByCourseId($id);

        $this->assertTrue($subject instanceof Collection);
    }
}
