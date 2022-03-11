<?php

namespace Tests\Feature\Exam;

use App\Models\Auth\User;
use App\Models\Exam\ExamFolder;
use App\Models\Exam\ExamQuestion;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ExamQuestionControllerTest extends TestCase
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

    public function testCanShow()
    {
        $question = ExamQuestion::factory()->create();
        $response = $this->get('/api/exam/question/'.$question->id);

        $response->assertOk();
    }

    public function testShowCanReturnNotFound()
    {
        $question = ExamQuestion::factory()->create();
        $response = $this->get('/api/exam/question/'.$question->id + 1);

        $response->assertNotFound();
    }

    public function testCanStore()
    {
        $response = $this->post(
            '/api/exam/question',
            [
                'context' =>'test',
                'metadata' => [
                    'option' => [],
                    'answer' => [],
                ],
                'answer_detail' => 'test',
                'type' => ExamQuestion::TYPE_ESSAY,
            ]
        );

        $response->assertCreated();
    }

    public function testStoreCanVerifyTrueFalseMetadata()
    {
        $response = $this->post(
            '/api/exam/question',
            [
                'context' =>'test',
                'metadata' => [
                    'option' => ['False', 'True'],
                    'answer' => [true],
                ],
                'answer_detail' => 'test',
                'type' => ExamQuestion::TYPE_TRUEFALSE,
            ]
        );

        $response->assertCreated();
    }

    public function testStoreCanVerifyTrueFalseMetadataWithWrongAnswer()
    {
        $response = $this->post(
            '/api/exam/question',
            [
                'context' =>'test',
                'metadata' => [
                    'option' => ['False', 'True'],
                    'answer' => [2],
                ],
                'answer_detail' => 'test',
                'type' => ExamQuestion::TYPE_TRUEFALSE,
            ]
        );

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testStoreCanVerifyChoiceMetadata()
    {
        $response = $this->post(
            '/api/exam/question',
            [
                'context' =>'test',
                'metadata' => [
                    'option' => ['Apple', 'Banana', 'Cap'],
                    'answer' => [0],
                ],
                'answer_detail' => 'test',
                'type' => ExamQuestion::TYPE_CHOICE,
            ]
        );

        $response->assertCreated();
    }

    public function testStoreCanVerifyChoiceMetadataWithAnswerOutOfUpperBound()
    {
        $response = $this->post(
            '/api/exam/question',
            [
                'context' =>'test',
                'metadata' => [
                    'option' => ['Apple', 'Banana', 'Cap'],
                    'answer' => [3],
                ],
                'answer_detail' => 'test',
                'type' => ExamQuestion::TYPE_CHOICE,
            ]
        );

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testStoreCanVerifyChoiceMetadataWithAnswerOutOfLowerBound()
    {
        $response = $this->post(
            '/api/exam/question',
            [
                'context' =>'test',
                'metadata' => [
                    'option' => ['Apple', 'Banana', 'Cap'],
                    'answer' => [3],
                ],
                'answer_detail' => 'test',
                'type' => ExamQuestion::TYPE_CHOICE,
            ]
        );

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testStoreCanVerifyChoiceMetadataWithNoOption()
    {
        $response = $this->post(
            '/api/exam/question',
            [
                'context' =>'test',
                'metadata' => [
                    'option' => [],
                    'answer' => [0],
                ],
                'answer_detail' => 'test',
                'type' => ExamQuestion::TYPE_CHOICE,
            ]
        );

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testStoreCanVerifyChoiceMetadataWithNoAnswer()
    {
        $response = $this->post(
            '/api/exam/question',
            [
                'context' =>'test',
                'metadata' => [
                    'option' => ['Apple', 'Banana', 'Cap'],
                    'answer' => [],
                ],
                'answer_detail' => 'test',
                'type' => ExamQuestion::TYPE_CHOICE,
            ]
        );

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testStoreCanVerifyChoiceMetadataWithManyAnswer()
    {
        $response = $this->post(
            '/api/exam/question',
            [
                'context' =>'test',
                'metadata' => [
                    'option' => ['Apple', 'Banana', 'Cap'],
                    'answer' => [0, 2],
                ],
                'answer_detail' => 'test',
                'type' => ExamQuestion::TYPE_CHOICE,
            ]
        );

        $response->assertCreated();
    }

    public function testStoreCanVerifyFillMetadata()
    {
        $response = $this->post(
            '/api/exam/question',
            [
                'context' =>'test',
                'metadata' => [
                    'option' => [],
                    'answer' => ['Apple', 'Banana', 'Cap'],
                ],
                'answer_detail' => 'test',
                'type' => ExamQuestion::TYPE_FILL,
            ]
        );

        $response->assertCreated();
    }

    public function testStoreCanVerifyFillMetadataWithNoAnswer()
    {
        $response = $this->post(
            '/api/exam/question',
            [
                'context' =>'test',
                'metadata' => [
                    'option' => [],
                    'answer' => [],
                ],
                'answer_detail' => 'test',
                'type' => ExamQuestion::TYPE_FILL,
            ]
        );

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testStoreCanVerifyFillMetadataWithOneAnswer()
    {
        $response = $this->post(
            '/api/exam/question',
            [
                'context' =>'test',
                'metadata' => [
                    'option' => [],
                    'answer' => ['Hi'],
                ],
                'answer_detail' => 'test',
                'type' => ExamQuestion::TYPE_FILL,
            ]
        );

        $response->assertCreated();
    }

    public function testStoreCanVerifyEssayMetadata()
    {
        $response = $this->post(
            '/api/exam/question',
            [
                'context' =>'test',
                'metadata' => [
                    'option' => [],
                    'answer' => [],
                ],
                'answer_detail' => 'test',
                'type' => ExamQuestion::TYPE_ESSAY,
            ]
        );

        $response->assertCreated();
    }

    public function testUpdateCanVerifyMetadata()
    {
        $question = ExamQuestion::factory()->withType(ExamQuestion::TYPE_TRUEFALSE)->create();
        $answer = $question->metadata['answer'];

        $response = $this->patch(
            '/api/exam/question/'.$question->id,
            [
                'metadata' => [
                    'option' => ['False', 'True'],
                    'answer' => [! $answer],
                ],
            ]
        );

        $response->assertNoContent();
    }

    public function testUpdateCanVerifyWithWrongMetadata()
    {
        $question = ExamQuestion::factory()->withType(ExamQuestion::TYPE_TRUEFALSE)->create();
        $answer = $question->metadata['answer'];

        $response = $this->patch(
            '/api/exam/question/'.$question->id,
            [
                'metadata' => [
                    'option' => ['False', 'True'],
                    'answer' => [123],
                ],
            ],
        );

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testUpdateCanVerifyWithWrongType()
    {
        $question = ExamQuestion::factory()->withType(ExamQuestion::TYPE_CHOICE)->create();
        $answer = $question->metadata['answer'];

        $response = $this->patch(
            '/api/exam/question/'.$question->id,
            [
                'type' => ExamQuestion::TYPE_TRUEFALSE,
            ],
        );

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testUpdateCanReturnNotFound()
    {
        $question = ExamQuestion::factory()->withType(ExamQuestion::TYPE_TRUEFALSE)->create();

        $response = $this->patch(
            '/api/exam/question/'.$question->id + 1,
            [
                'type' => ExamQuestion::TYPE_TRUEFALSE,
            ],
        );

        $response->assertNotFound();
    }

    public function testCanDelete()
    {
        $question = ExamQuestion::factory()->create();
        $response = $this->delete('/api/exam/question/'.$question->id);

        $response->assertNoContent();
    }
}
