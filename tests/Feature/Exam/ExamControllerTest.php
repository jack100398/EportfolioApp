<?php

namespace Tests\Feature\Exam;

use App\Models\Auth\User;
use App\Models\Exam\Exam;
use App\Models\Exam\ExamQuestion;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExamControllerTest extends TestCase
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
        $response = $this->get('/api/exam');

        $response->assertOk();
    }

    public function testCanShow()
    {
        $exam = Exam::factory()->create();
        $response = $this->get('/api/exam/'.$exam->id);

        $response->assertOk();
    }

    public function testCanStore()
    {
        $exam = Exam::factory()->make()->toArray();
        $response = $this->post('/api/exam/', $exam);

        $this->assertTrue($response->json()['id'] > 0);
    }

    public function testShowCanReturnNotFound()
    {
        $exam = Exam::factory()->create();
        $response = $this->get('/api/exam/'.$exam->id + 1);

        $response->assertNotFound();
    }

    public function testCanUpdate()
    {
        $exam = Exam::factory()->create();
        $response = $this->patch('/api/exam/'.$exam->id, [
            'title' => 'newTitle',
        ]);

        $response->assertNoContent();
    }

    public function testUpdateCanReturnNotFound()
    {
        $exam = Exam::factory()->create();
        $response = $this->patch('/api/exam/'.$exam->id + 1, [
            'title' => 'newTitle',
        ]);

        $response->assertNotFound();
    }

    public function testCanDelete()
    {
        $exam = Exam::factory()->create();
        $response = $this->delete('/api/exam/'.$exam->id);

        $response->assertNoContent();
    }

    public function testCanShowWithoutAnswer()
    {
        $exam = Exam::factory()
            ->hasAttached(
                ExamQuestion::factory(5),
                ['score' => 10, 'sequence' => 0]
            )
            ->create();
        $response = $this->get("/api/exam/$exam->id/show");

        $response->assertOk();
    }

    public function testCanGetTemplateExam()
    {
        Exam::factory(2)->create(['is_template'=>true]);
        Exam::factory(2)->create(['is_template'=>false]);

        $response = $this->get('/api/exam/template');

        $response->assertOk();
    }
}
