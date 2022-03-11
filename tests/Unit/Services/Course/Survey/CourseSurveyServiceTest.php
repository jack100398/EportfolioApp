<?php

namespace Tests\Unit\Services\Course\Survey;

use App\Models\Auth\User;
use App\Models\Course\Survey\CourseSurvey;
use App\Services\Course\Survey\CourseSurveyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Throwable;

class CourseSurveyServiceTest extends TestCase
{
    use RefreshDatabase;

    private CourseSurveyService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new CourseSurveyService();
    }

    public function testCanGet()
    {
        $id = CourseSurvey::factory()->create()->id;

        $subject = $this->service->getById($id);

        $this->assertTrue($subject instanceof CourseSurvey);
    }

    public function testCanUpdate()
    {
        $id = CourseSurvey::factory()->create()->id;

        $originEndTime = $this->service->getById($id)->end_at;

        $this->service->update($id, ['end_at' => now()->addDays(20)]);

        $this->assertTrue($this->service->getById($id)->end_at !== $originEndTime);
    }

    public function testCanCreate()
    {
        $data = CourseSurvey::factory()->make();

        $id = $this->service->create($data->toArray());

        $this->assertTrue($id > 0);
    }

    public function testCanDelete()
    {
        $id = CourseSurvey::factory()->create()->id;
        $this->assertTrue($id > 0);
        $this->service->delete($id);
        $this->assertNull(CourseSurvey::find($id));
    }
}
