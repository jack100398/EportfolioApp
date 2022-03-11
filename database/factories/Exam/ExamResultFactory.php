<?php

namespace Database\Factories\Exam;

use App\Models\Auth\User;
use App\Models\Exam\Exam;
use App\Models\Exam\ExamQuestion;
use App\Models\Exam\ExamResult;
use Database\Factories\Helper\FactoryHelper;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamResultFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExamResult::class;

    private ?int $score = null;

    private ?Exam $exam = null;

    private bool $isMarked = false;

    private bool $isFinished = false;

    private array $metadata = [];

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'exam_id'     => $this->getExam()->id,
            'user_id'     => User::factory()->create()->id,
            'metadata'    => $this->getMetadata(),
            'score'       => $this->score,
            'is_marked'   => $this->isMarked ?? $this->faker->boolean(),
            'is_finished' => $this->isFinished ?? $this->faker->boolean(),
            'start_time'  => $this->faker->date(),
            'end_time'    => $this->faker->date(),
            'source_ip'   => $this->faker->ipv4(),
        ];
    }

    public function withExam(int $examId)
    {
        $this->exam = Exam::find($examId);

        return $this;
    }

    public function withCorrectAnswer()
    {
        $questions = $this->getExam()->examQuestions;
        $metadata = [];
        foreach ($questions as $question) {
            $metadata[$question->pivot->id] = [
                'answer' => $question->metadata['answer'],
                'score' => 0,
            ];
            $this->score += $question->pivot->score;
        }

        $this->metadata = $metadata;

        return $this;
    }

    public function withoutAnswer()
    {
        $answers = [];

        $questions = $this->getExam()->examQuestions;
        foreach ($questions as $question) {
            $answers[$question->pivot->id] = ['answer' => [], 'score' => 0];
        }

        $this->metadata = $answers;

        return $this;
    }

    public function finished()
    {
        $this->isMarked = false;
        $this->isFinished = true;
        $this->score = null;

        return $this;
    }

    public function marked()
    {
        $this->isMarked = true;
        $this->isFinished = true;

        return $this;
    }

    public function unfinished()
    {
        $this->isMarked = false;
        $this->isFinished = false;
        $this->score = null;

        return $this;
    }

    private function createExamWithQuestions()
    {
        return Exam::factory()
            ->hasAttached(
                ExamQuestion::factory(4),
                ['score' => 25, 'sequence' => 0]
            )
            ->create();
    }

    private function getExam()
    {
        if ($this->exam === null) {
            $this->exam = $this->createExamWithQuestions();
        }

        return $this->exam;
    }

    private function getMetadata()
    {
        if ($this->metadata === []) {
            $this->withoutAnswer();
        }

        return $this->metadata;
    }
}
