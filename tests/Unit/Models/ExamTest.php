<?php

namespace Tests\Unit\Models;

use App\Models\Exam\Exam;
use App\Models\Exam\ExamFolder;
use App\Models\Exam\ExamQuestion;
use App\Models\Exam\ExamResult;
use Faker\Factory;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamTest extends TestCase
{
    use RefreshDatabase;

    public function testFolderHasQuestions(): void
    {
        $folder = ExamFolder::factory()
            ->has(ExamQuestion::factory(5))
            ->create();

        $question_count = $folder->examQuestions->count();

        $this->assertTrue($question_count === 5);
    }

    public function testHasChildFolder(): void
    {
        $folder = ExamFolder::factory()
            ->has(ExamFolder::factory(3), 'children')
            ->create();

        $folder_count = $folder->children->count();

        $this->assertTrue($folder_count === 3);
    }

    public function testHasParentFolder(): void
    {
        $folder = ExamFolder::factory()
            ->for(ExamFolder::factory(), 'parent')
            ->create();

        $parent_folder = $folder->parent;

        $this->assertTrue($parent_folder->id === $folder->parent_id);
    }

    public function testExamAndResultHasRelations(): void
    {
        $exam = Exam::factory()
            ->has(ExamResult::factory())
            ->create();

        $result = $exam->examResults->first();

        $this->assertTrue($result->exam->id === $exam->id);
    }

    public function testExamHasQuestions(): void
    {
        $exam = Exam::factory()
            ->hasAttached(ExamQuestion::factory(3), [
                'score' => 10,
                'sequence' => 0,
            ])
            ->create();

        $this->assertTrue($exam->examQuestions->count() === 3);
    }
}
