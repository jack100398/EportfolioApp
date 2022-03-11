<?php

namespace Tests\Feature\Course\Survey;

use App\Models\Auth\User;
use App\Models\Course\Survey\CourseSurvey;
use App\Models\Course\Survey\CourseSurveyRecord;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CourseSurveyRecordControllerTest extends TestCase
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

    public function testCanCreate()
    {
        $response = $this->post('/api/courseSurveyRecord', [
            'course_survey_id' => CourseSurvey::factory()->create()->id,
            'role_type' => 1,
            'metadata' => [1, 2, 3],
        ]);

        $response->assertCreated();
    }

    public function testCanUpdate()
    {
        $response = $this->put('/api/courseSurveyRecord/'.CourseSurveyRecord::factory()->create()->id, [
            'metadata' => [3, 2, 1],
        ]);
        $response->assertNoContent();
    }

    public function testCanShow()
    {
        $response = $this->get('/api/courseSurveyRecord/'.CourseSurveyRecord::factory()->create()->id);
        $response->assertOk();
    }

    public function testCanDelete()
    {
        $response = $this->delete('/api/courseSurveyRecord/'.CourseSurveyRecord::factory()->create()->id);
        $response->assertNoContent();
    }
}
