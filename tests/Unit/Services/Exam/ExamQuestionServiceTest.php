<?php

namespace Tests\Unit\Services\Exam;

use App\Models\Exam\ExamQuestion;
use App\Services\Exam\ExamQuestionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamQuestionServiceTest extends TestCase
{
    use RefreshDatabase;

    private ExamQuestionService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new ExamQuestionService();
    }

    public function testUpdateCanThrowException()
    {
        $this->expectException(ModelNotFoundException::class);

        $question = ExamQuestion::factory()->create();
        $this->service->update($question->id + 1, [
            'context' => 'newContext',
        ]);
    }
}
