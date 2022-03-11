<?php

namespace Tests\Feature\Course\Survey;

use App\Models\Auth\User;
use App\Models\Course\Survey\Survey;
use App\Models\Course\Survey\SurveyQuestion;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SurveyQuestionControllerTest extends TestCase
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
        $response = $this->post('/api/surveyQuestion', [
            'survey_id' => Survey::factory()->create()->id,
            'content' => 'required|string',
            'sort' => 0,
            'type' => 0,
            'option_content' => ['aaa', 'bbb', 'ccc', 'ddd'],
            'option_score' => [1, 2, 3, 4],
        ]);

        $response->assertCreated();
    }

    public function testCanUpdate()
    {
        $response = $this->put('/api/surveyQuestion/'.SurveyQuestion::factory()->create()->id, [
            'survey_id' => Survey::factory()->create()->id,
            'content' => 'required|string',
            'sort' => 0,
            'type' => 0,
            'option_content' => ['ddd', 'ccc', 'bbb', 'aaa'],
            'option_score' => [1, 2, 3, 4],
        ]);
        $response->assertNoContent();
    }

    public function testCanShow()
    {
        $response = $this->get('/api/surveyQuestion/'.SurveyQuestion::factory()->create()->id);

        $response->assertOk();
    }

    public function testCanDelete()
    {
        $response = $this->delete('/api/surveyQuestion/'.SurveyQuestion::factory()->create()->id);

        $response->assertNoContent();
    }
}
