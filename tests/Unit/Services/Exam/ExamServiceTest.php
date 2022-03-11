<?php

namespace Tests\Unit\Services\Exam;

use App\Models\Auth\User;
use App\Models\Exam\Exam;
use App\Models\Exam\ExamFolder;
use App\Models\Exam\ExamQuestion;
use App\Models\Exam\Pivot\ExamQuestionPivot;
use App\Services\Exam\ExamService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Concerns\TestDatabases;
use function PHPUnit\Framework\assertNotSame;
use Tests\TestCase;

class ExamServiceTest extends TestCase
{
    use RefreshDatabase;

    private ExamService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new ExamService();
    }

    public function testCanGet()
    {
        $exam = Exam::factory()->create();

        $subject = $this->service->getById($exam->id);

        $this->assertTrue($subject instanceof Exam);
    }

    public function testCanInsert()
    {
        $data = Exam::factory()->make()->toArray();

        $id = $this->service->create($data, User::factory()->create()->id);

        $this->assertTrue($id > 0);
    }

    public function testCanUpdate()
    {
        $id = Exam::factory()->create(['title' => 'old'])->id;

        $update = ['title' => 'new'];
        $this->service->update($id, $update);

        $result = Exam::find($id);
        $this->assertSame('new', $result->title);
    }

    public function testCanDelete()
    {
        $id = Exam::factory()->create()->id;

        $this->service->deleteById($id);

        $result = Exam::find($id);
        $this->assertNull($result);
    }

    public function testCanInsertQuestionsIntoExam()
    {
        // Arrange
        $exam = Exam::factory()->create();
        $questions = ExamQuestion::factory(5)->create();
        $data = [];
        $sequence = 0;
        // Act
        foreach ($questions as $question) {
            $data[$question->id] = [
                'sequence' => $sequence++,
                'score' => 10,
            ];
        }
        $this->service->syncQuestionsIntoExam($exam->id, $data);
        // Assert
        $this->assertSame(5, $exam->examQuestions()->count());
    }

    public function testChangeExamQuestionWillCreateNewQuestion()
    {
        // Arrange
        $exam = Exam::factory()->create();
        $question = ExamQuestion::factory()->create([
            'type' => ExamQuestion::TYPE_TRUEFALSE,
            'metadata' => [
                'option' => ['False', 'True'],
                'answer' => [true],
            ],
        ]);
        $exam->examQuestions()->attach($question, ['score'=>10, 'sequence'=>0]);
        // Act
        $newMetadata = [
            'option' => ['False', 'True'],
            'answer' => [false],
        ];
        $this->service->changeExamQuestionOption($exam->id, $question->id, $newMetadata);
        // Assert
        $newQuestion = $exam->examQuestions()->first();
        $this->assertNotSame($newQuestion->metadata, $question->metadata);
    }

    public function testChangeExamQuestionWillChangePivotValue()
    {
        // Arrange
        $exam = Exam::factory()->create();
        $question = ExamQuestion::factory()->create([
            'type' => ExamQuestion::TYPE_TRUEFALSE,
            'metadata' => [
                'option' => ['False', 'True'],
                'answer' => [true],
            ],
        ]);
        $exam->examQuestions()->attach($question, ['score'=>10, 'sequence'=>0]);
        // Act
        $newMetadata = [
            'option' => ['False', 'True'],
            'answer' => [false],
        ];
        $this->service->changeExamQuestionOption($exam->id, $question->id, $newMetadata);
        // Assert
        $newQuestion = $exam->examQuestions()->first();
        $this->assertNotSame($newQuestion->id, $question->id);
    }

    public function testChangeExamQuestionWillNotChangePivotId()
    {
        // Arrange
        $exam = Exam::factory()->create();
        $question = ExamQuestion::factory()
            ->withType(ExamQuestion::TYPE_TRUEFALSE)
            ->create();
        $exam->examQuestions()->attach($question, ['score'=>10, 'sequence'=>0]);

        $pivot = ExamQuestionPivot::where('exam_id', '=', $exam->id)
            ->where('question_id', '=', $question->id)
            ->first();
        // Act
        $newMetadata = [
            'option' => ['False', 'True'],
            'answer' => [! $question->metadata['answer'][0]],
        ];
        $this->service->changeExamQuestionOption($exam->id, $question->id, $newMetadata);
        // Assert
        $newQuestion = $exam->examQuestions()->first();
        $newPivot = ExamQuestionPivot::where('exam_id', '=', $exam->id)
            ->where('question_id', '=', $newQuestion->id)
            ->first();
        $this->assertSame($pivot->id, $newPivot->id);
    }

    public function testUpdateCanThrowException()
    {
        $this->expectException(ModelNotFoundException::class);

        $exam = Exam::factory()->create();
        $result = $this->service->update($exam->id + 1, [
            'title' => 'newTitle',
        ]);
    }

    public function testSyncQuestionsCanDetectError()
    {
        // Arrange
        $exam = Exam::factory()->create();
        $questions = ExamQuestion::factory(5)->create();
        $data = [];
        $sequence = 0;
        // Act
        foreach ($questions as $question) {
            $data[$question->id] = [
                'sequence' => $sequence++,
                'score' => 10,
            ];
        }
        $data[-1] = [
            'sequence' => $sequence++,
            'score' => 10,
        ];
        $result = $this->service->syncQuestionsIntoExam($exam->id, $data);
        // Assert
        $this->assertFalse($result);
    }

    public function testChangeExamQuestionAnswer()
    {
        $question = ExamQuestion::factory()->withType(ExamQuestion::TYPE_FILL);
        $exam = Exam::factory()->hasAttached($question, ['score' => 10, 'sequence' => 0])->create();
        $metadata = ['option' => [], 'answer' => ['apple', 'banana', 'cap']];

        $result = $this->service->changeExamQuestionOption(
            $exam->id,
            $exam->examQuestions->first()->id,
            $metadata
        );

        $this->assertTrue($result);
    }

    public function testChangeExamQuestionAnswerCanDetectWrongMetadata()
    {
        $question = ExamQuestion::factory()->withType(ExamQuestion::TYPE_FILL);
        $exam = Exam::factory()->hasAttached($question, ['score' => 10, 'sequence' => 0])->create();
        $metadata = ['option' => [], 'answer' => []];

        $result = $this->service->changeExamQuestionOption(
            $exam->id,
            $exam->examQuestions->first()->id,
            $metadata
        );

        $this->assertFalse($result);
    }

    public function testChangeQuestionScore()
    {
        $question = ExamQuestion::factory()->withType(ExamQuestion::TYPE_FILL);
        $exam = Exam::factory()
            ->hasAttached($question, ['score' => 10, 'sequence' => 0])
            ->create(['passed_score' => 10]);

        $result = $this->service->changeQuestionScore(
            $exam->id,
            $exam->examQuestions->first()->id,
            100
        );

        $this->assertTrue($result);
    }

    public function testChangeQuestionScoreCanDetectTotalScoreLowerThanPassedScore()
    {
        $question = ExamQuestion::factory()->withType(ExamQuestion::TYPE_FILL);
        $exam = Exam::factory()
            ->hasAttached($question, ['score' => 100, 'sequence' => 0])
            ->create(['passed_score' => 100]);

        $result = $this->service->changeQuestionScore(
            $exam->id,
            $exam->examQuestions->first()->id,
            50
        );

        $this->assertFalse($result);
    }

    public function testChangeQuestionScoreCanDetectNoExam()
    {
        $question = ExamQuestion::factory()->withType(ExamQuestion::TYPE_FILL);
        $exam = Exam::factory()
            ->hasAttached($question, ['score' => 100, 'sequence' => 0])
            ->create(['passed_score' => 10]);

        $result = $this->service->changeQuestionScore(
            $exam->id + 1,
            $exam->examQuestions->first()->id,
            100
        );

        $this->assertFalse($result);
    }

    public function testCanGetTemplateExam()
    {
        Exam::factory(2)->create(['is_template'=>true]);
        Exam::factory(2)->create(['is_template'=>false]);

        $result = $this->service->getTemplateExams();

        $this->assertSame(2, $result->count());
    }
}
