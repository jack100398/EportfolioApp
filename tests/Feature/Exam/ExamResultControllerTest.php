<?php

namespace Tests\Feature\Exam;

use App\Models\Auth\User as AuthUser;
use App\Models\Exam\Exam;
use App\Models\Exam\ExamQuestion;
use App\Models\Exam\ExamResult;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExamResultControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        Sanctum::actingAs(
            AuthUser::factory()->create(),
            ['*']
        );
    }

    public function testCanShow()
    {
        $result = ExamResult::factory()->create();
        $response = $this->get('/api/exam/result/'.$result->id);

        $response->assertOk();
    }

    public function testCanStore()
    {
        $result = ExamResult::factory()->make()->toArray();

        $response = $this->post('/api/exam/result/', $result);
        $this->assertTrue($response->json()['id'] > 0);
    }

    public function testShowCanReturnNotFound()
    {
        $result = ExamResult::factory()->create();
        $response = $this->get('/api/exam/result/'.$result->id + 1);

        $response->assertNotFound();
    }

    public function testCanUpdate()
    {
        $result = ExamResult::factory()->create();
        $response = $this->patch('/api/exam/result/'.$result->id, [
            'score' => 10000,
        ]);

        $response->assertNoContent();
    }

    public function testUpdateCanReturnNotFound()
    {
        $result = ExamResult::factory()->create();
        $response = $this->patch('/api/exam/result/'.$result->id + 1, [
            'score' => 10000,
        ]);

        $response->assertNotFound();
    }

    public function testCanDelete()
    {
        $result = ExamResult::factory()->create();
        $response = $this->delete('/api/exam/result/'.$result->id);

        $response->assertNoContent();
    }

    public function testCanAutoMarkScore()
    {
        // arrange
        $exam = Exam::factory()
            ->hasAttached(ExamQuestion::factory(5)->withType(ExamQuestion::TYPE_TRUEFALSE), ['score' => 20, 'sequence' => 0])
            ->create();
        $result = ExamResult::factory()
            ->withExam($exam->id)
            ->withCorrectAnswer()
            ->finished()
            ->create();
        // act
        $response = $this->post("api/exam/result/$result->id/autoMark");
        // assert
        $response->assertNoContent();
    }

    public function testAutoMarkScoreCanDetectUnfinishedResult()
    {
        // arrange
        $result = ExamResult::factory()
            ->withCorrectAnswer()
            ->unfinished()
            ->create();
        // act
        $response = $this->post("api/exam/result/$result->id/autoMark");
        // assert
        $response->assertNotFound();
    }

    public function testCanManualMarkScore()
    {
        // arrange
        $exam = Exam::factory()
            ->hasAttached(ExamQuestion::factory(5)->withType(ExamQuestion::TYPE_TRUEFALSE), ['score' => 20, 'sequence' => 0])
            ->create();

        $result = ExamResult::factory()->withExam($exam->id)->finished()->create();
        // act
        $scores = [];
        foreach ($exam->examQuestions as $question) {
            $scores[$question->pivot->id] = 20;
        }
        $reponse = $this->post("api/exam/result/$result->id/manualMark", $scores);
        // assert
        $reponse->assertNoContent();
    }

    public function testManualMarkScoreCanDetectUnfinishedResult()
    {
        // arrange
        $result = ExamResult::factory()
            ->withCorrectAnswer()
            ->unfinished()
            ->create();
        // act
        $response = $this->post("api/exam/result/$result->id/manualMark");
        // assert
        $response->assertNotFound();
    }
}
