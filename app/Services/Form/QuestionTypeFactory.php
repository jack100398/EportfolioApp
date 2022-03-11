<?php

namespace App\Services\Form;

use App\Services\Form\QuestionTypes\AbilityScore;
use App\Services\Form\QuestionTypes\AutoFill;
use App\Services\Form\QuestionTypes\BaseQuestionType;
use App\Services\Form\QuestionTypes\CalculateScore;
use App\Services\Form\QuestionTypes\Canvas;
use App\Services\Form\QuestionTypes\Date;
use App\Services\Form\QuestionTypes\Description;
use App\Services\Form\QuestionTypes\EssayQuestion;
use App\Services\Form\QuestionTypes\Feedback;
use App\Services\Form\QuestionTypes\FormDescription;
use App\Services\Form\QuestionTypes\Image;
use App\Services\Form\QuestionTypes\Interfaces\IBaseQuestionType;
use App\Services\Form\QuestionTypes\MultipleChoice;
use App\Services\Form\QuestionTypes\MultiSelectQuestion;
use App\Services\Form\QuestionTypes\Question;
use App\Services\Form\QuestionTypes\QuestionGroup;
use App\Services\Form\QuestionTypes\Satisfaction;
use App\Services\Form\QuestionTypes\Signature;
use App\Services\Form\QuestionTypes\TrueOrFalse;
use App\Services\Form\QuestionTypes\UploadFile;
use Exception;

class QuestionTypeFactory extends BaseQuestionType
{
    /**
     * Class contractor.
     *
     * @param array $question
     *
     * @return array ['type'=> '', 'attributes'=>[]]
     */
    public function getQuestionType(array $question): array
    {
        return ! isset($question['type']) && ! isset($question['attributes']) ? []
        : $this->make($question);
    }

    private function make(array $question): array
    {
        $this->type = $question['type'];
        $this->attributes = $this->switchQuestionType()->transferQuestion($question['attributes']);

        return $this->transferBaseQuestionType();
    }

    private function switchQuestionType(): IBaseQuestionType
    {
        $questionTypes = [
            2 => new Description(),
            3 => new Question(),
            4 => new FormDescription(),
            5 => new QuestionGroup(),
            6 => new MultipleChoice(),
            7 => new MultiSelectQuestion(),
            8 => new EssayQuestion(),
            9 => new AbilityScore(),
            10 => new Satisfaction(),
            11 => new UploadFile(),
            12 => new Canvas(),
            13 => new Date(),
            14 => new Feedback(),
            15 => new Signature(),
            16 => new Image(),
            17 => new CalculateScore(),
            18 => new TrueOrFalse(),
            19 => new AutoFill(),
        ];

        return array_key_exists($this->type, $questionTypes) ? new $questionTypes[$this->type]()
        : throw new Exception('UnknownQuestionType');
    }
}
