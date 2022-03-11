<?php

namespace Database\Transfers\Form\FormQuestionTypeFactory;

use Database\Transfers\Form\FormQuestionTypeFactory\Interfaces\IFormQuestionTypeFactory;
use Database\Transfers\Form\QuestionTypes\AbilityScore;
use Database\Transfers\Form\QuestionTypes\AutoFill;
use Database\Transfers\Form\QuestionTypes\BaseQuestion;
use Database\Transfers\Form\QuestionTypes\CalculateScore;
use Database\Transfers\Form\QuestionTypes\Canvas;
use Database\Transfers\Form\QuestionTypes\Date;
use Database\Transfers\Form\QuestionTypes\Description;
use Database\Transfers\Form\QuestionTypes\EssayQuestion;
use Database\Transfers\Form\QuestionTypes\Feedback;
use Database\Transfers\Form\QuestionTypes\FormDescription;
use Database\Transfers\Form\QuestionTypes\Image;
use Database\Transfers\Form\QuestionTypes\Interfaces\IBaseOption;
use Database\Transfers\Form\QuestionTypes\Interfaces\IBaseQuestionType;
use Database\Transfers\Form\QuestionTypes\MultipleChoice;
use Database\Transfers\Form\QuestionTypes\MultiSelectQuestion;
use Database\Transfers\Form\QuestionTypes\Question;
use Database\Transfers\Form\QuestionTypes\QuestionGroup;
use Database\Transfers\Form\QuestionTypes\Satisfaction;
use Database\Transfers\Form\QuestionTypes\Signature;
use Database\Transfers\Form\QuestionTypes\TrueOrFalse;
use Database\Transfers\Form\QuestionTypes\UploadFile;
use Exception;

class FormQuestionTypeFactory extends BaseQuestion implements IFormQuestionTypeFactory
{
    private function makeQuestionType(int $ques_num): IBaseQuestionType
    {
        switch ($this->type) {
            case 2:
                return ($ques_num === 0) ? new FormDescription() :
                new Description();

            case 3:
                return new Question();

            case 5:
                return new QuestionGroup();

            case 6:
                return new MultipleChoice();

            case 7:
                return new MultiSelectQuestion();

            case 8:
                return new EssayQuestion();

            case 9:
                return new AbilityScore();

            case 10:
                return new Satisfaction();

            case 11:
                return new UploadFile();

            case 12:
                return new Canvas();

            case 13:
                return new Date();

            case 14:
                return new Feedback();

            case 15:
                return new Signature();

            case 16:
                return new Image();

            case 17:
                return new CalculateScore();

            case 18:
                return new TrueOrFalse();

            case 19:
                return new AutoFill();

            default:
                throw new Exception('UnknowQuestionType');
        }
    }

    public function transferQuestionType(array $transferDatas): array
    {
        $this->type = $transferDatas['question']['ques_type'];
        $this->attributes = $this->makeQuestionType($transferDatas['question']['ques_num'])
        ->transferQuestion($transferDatas);

        return $this->transferBaseQuestionType();
    }

    private function makeQuestionOption(int $type): IBaseOption
    {
        switch ($type) {

            case 6:
                return new MultipleChoice();

            case 7:
                return new MultiSelectQuestion();

            case 18:
                return new TrueOrFalse();

            default:
                throw new Exception('UnknowQuestionType');
        }
    }

    public function pushQuestion(array $questionType, array $question): array
    {
        return $this->makeQuestionOption($questionType['type'])->pushOption($questionType, $question);
    }
}
