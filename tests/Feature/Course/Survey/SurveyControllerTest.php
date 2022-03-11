<?php

namespace Tests\Feature\Course\Survey;

use App\Models\Auth\User;
use App\Models\Course\Survey\Survey;
use App\Models\Course\Survey\SurveyQuestion;
use App\Models\Unit;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SurveyControllerTest extends TestCase
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
        $response = $this->get('/api/survey');

        $response->assertOk();
    }

    public function testCanCreate()
    {
        $response = $this->post('/api/survey', [
            'name' => 'testing',
            'public' => true,
            'unit_id' => Unit::factory()->create()->id,
        ]);

        $response->assertCreated();
    }

    public function testCanCreateNewVersion()
    {
        $response = $this->post('/api/survey', [
            'name' => 'testing',
            'public' => true,
            'origin' => Survey::factory()->create()->id,
            'unit_id' => Unit::factory()->create()->id,
        ]);

        $response->assertCreated();
    }

    public function testCanUpdate()
    {
        $response = $this->put('/api/survey/'.Survey::factory()->create()->id, [
            'name' => 'testing',
            'public' => true,
            'unit_id' => Unit::factory()->create()->id,
        ]);
        $response->assertNoContent();
    }

    public function testCanShow()
    {
        $surveyId = Survey::factory()->create()->id;
        SurveyQuestion::factory(10)->create(['survey_id' => $surveyId]);

        $response = $this->get('/api/survey/'.$surveyId);
        $response->assertOk();
    }

    public function testCanDelete()
    {
        $response = $this->delete('/api/survey/'.Survey::factory()->create()->id);
        $response->assertNoContent();
    }

    public function testCanCopy()
    {
        $response = $this->put('/api/survey/copy/'.Survey::factory()->create()->id);

        $response->assertCreated();
    }
}
