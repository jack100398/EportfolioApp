<?php

namespace Tests\Unit\Services\Exam;

use App\Models\Exam\Exam;
use App\Models\Exam\ExamQuestion;
use App\Models\Exam\ExamResult;
use App\Models\Exam\Pivot\ExamQuestionPivot;
use App\Services\Exam\ExamResultService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamResultServiceTest extends TestCase
{
    use RefreshDatabase;

    private ExamResultService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new ExamResultService();
    }

    public function testCanGet()
    {
        $exam = ExamResult::factory()->create();

        $subject = $this->service->getById($exam->id);

        $this->assertTrue($subject instanceof ExamResult);
    }

    public function testCanInsert()
    {
        $data = ExamResult::factory()->make()->toArray();

        $id = $this->service->create($data);

        $this->assertTrue($id > 0);
    }

    public function testCanUpdate()
    {
        $id = ExamResult::factory()->create(['score' => 0])->id;

        $update = ['score' => 100];
        $this->service->update($id, $update);

        $result = ExamResult::find($id);
        $this->assertSame(100, $result->score);
    }

    public function testCanDelete()
    {
        $id = ExamResult::factory()->create()->id;

        $this->service->deleteById($id);

        $result = ExamResult::find($id);
        $this->assertNull($result);
    }

    public function testDeleteCanThrowException()
    {
        $this->expectException(ModelNotFoundException::class);

        $id = ExamResult::factory()->create()->id;

        $subject = $this->service->deleteById($id + 1);
    }

    public function testCanMakeAnswerMetadata()
    {
        $exam = Exam::factory()
            ->hasAttached(ExamQuestion::factory(5), ['score' => 20, 'sequence' => 0])
            ->create();

        $answers = $this->service->makeAnswerMetadata($exam->id);

        $pivotIds = ExamQuestionPivot::where('exam_id', $exam->id)->pluck('id')->toArray();
        $this->assertTrue(array_keys($answers) == $pivotIds);
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
        $this->service->autoMarkingResult($result->id);
        // assert
        $this->assertSame(100, $result->refresh()->score);
        $this->assertTrue($result->refresh()->is_marked);
    }

    public function testCanManualMarkScore()
    {
        // arrange
        $exam = Exam::factory()
            ->hasAttached(ExamQuestion::factory(5)->withType(ExamQuestion::TYPE_TRUEFALSE), ['score' => 20, 'sequence' => 0])
            ->create();

        $result = ExamResult::factory()
            ->withExam($exam->id)
            ->finished()
            ->create();
        // act
        $scores = [];
        foreach ($exam->examQuestions as $question) {
            $scores[$question->pivot->id] = 20;
        }
        $this->service->manualMarkingResult($result->id, $scores);
        // assert
        $this->assertSame(100, $result->refresh()->score);
    }

    public function testManualMarkScoreCanDetectUnfinishedResult()
    {
        // arrange
        $exam = Exam::factory()
            ->hasAttached(ExamQuestion::factory(5)->withType(ExamQuestion::TYPE_TRUEFALSE), ['score' => 20, 'sequence' => 0])
            ->create();

        $result = ExamResult::factory()
            ->withExam($exam->id)
            ->unfinished()
            ->create();
        // act
        $scores = [];
        foreach ($exam->examQuestions as $question) {
            $scores[$question->pivot->id] = 20;
        }
        $this->service->manualMarkingResult($result->id, $scores);
        // assert
        $this->assertFalse($result->refresh()->is_marked);
        $this->assertNull($result->refresh()->score);
    }

    public function testAutoMarkScoreCanThrowException()
    {
        $this->expectException(ModelNotFoundException::class);

        $result = ExamResult::factory()->create();

        $this->service->autoMarkingResult($result->id + 1);
    }

    public function testUpdateCanThrowException()
    {
        $this->expectException(ModelNotFoundException::class);

        $result = ExamResult::factory()->create();

        $this->service->update($result->id + 1, ['is_finished'=>true]);
    }

    public function testWontAutoMarkUnfinishedResult()
    {
        $result = ExamResult::factory()->unfinished()->create();

        $subject = $this->service->autoMarkingResult($result->id);

        $this->assertFalse($subject);
    }

    public function testResultWithDeletedExam()
    {
        $exam = Exam::factory()->create();
        $result = ExamResult::factory()->withExam($exam->id)->finished()->create();

        $exam->delete();
        $subject = $this->service->autoMarkingResult($result->id);

        $this->assertFalse($subject);
    }

    public function testCanMarkScoreWithAllQuestionTypes()
    {
        // arrange
        $exam = Exam::factory()
               ->hasAttached(ExamQuestion::factory()->withType(ExamQuestion::TYPE_CHOICE), ['score' => 20, 'sequence' => 0])
               ->hasAttached(ExamQuestion::factory()->withType(ExamQuestion::TYPE_ESSAY), ['score' => 20, 'sequence' => 0])
               ->hasAttached(ExamQuestion::factory()->withType(ExamQuestion::TYPE_FILL), ['score' => 20, 'sequence' => 0])
               ->hasAttached(ExamQuestion::factory()->withType(ExamQuestion::TYPE_TRUEFALSE), ['score' => 20, 'sequence' => 0])
               ->create();

        $result = ExamResult::factory()
               ->withExam($exam->id)
               ->withCorrectAnswer()
               ->finished()
               ->create();

        // act
        $this->service->autoMarkingResult($result->id);
        // assert
        $result->refresh();
        $this->assertFalse($result->is_marked);
        $this->assertSame(60, $result->score);
    }

    public function testCanDetectQuestionsCantBeAutoMarked()
    {
        // arrange
        $exam = Exam::factory()
                ->hasAttached(ExamQuestion::factory()->withType(ExamQuestion::TYPE_ESSAY), ['score' => 20, 'sequence' => 0])
                ->create();

        $result = ExamResult::factory()
                ->withExam($exam->id)
                ->withCorrectAnswer()
                ->finished()
                ->create();

        // act
        $this->service->autoMarkingResult($result->id);
        // assert
        $this->assertFalse($result->refresh()->is_marked);
    }
}
