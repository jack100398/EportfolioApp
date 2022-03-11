<?php

namespace Database\Factories\Form;

use App\Models\NominalRole\NominalRole;
use Illuminate\Http\UploadedFile;

class FormQuestionTypeFactory
{
    private function formDescribe(): array
    {
        return ['type'=>4,  'attributes'=>['content'=>'FormDescribe']];
    }

    private function questionGroup(): array
    {
        return ['type'=>5, 'attributes'=>['questions'=>$this->QuestionTypeFactory(), 'title' => 'QuestionGroup']];
    }

    private function image(): array
    {
        return ['type'=>16,  'attributes'=>['file'=>UploadedFile::fake()->image('avatar.jpg')]];
    }

    private function abilityScore(): array
    {
        return ['type'=>9, 'attributes'=>['title' =>'AbilityScore',  'require'=>false, 'max'=>0, 'min'=>0, 'targets'=>[NominalRole::factory()->create()->id, NominalRole::factory()->create()->id],
            'customAttributes'=>['range'=>0, 'max'=>0, 'defaultValue'=>0, 'showNAOption'=>true, 'questionStyle'=>1], ]];
    }

    private function autoFill(): array
    {
        return ['type'=>19, 'attributes'=>['title'=>'AutoFill',  'value'=>0, 'targets'=>[0]]];
    }

    private function calculateScore(): array
    {
        return ['type'=>17, 'attributes'=>['title'=>'CalculateScore',  'max'=>0, 'targets'=>[0], 'require'=>false]];
    }

    private function canvas(): array
    {
        return ['type'=>12, 'attributes'=>['title'=>'Canvas',  'targets'=>[0], 'require'=>false]];
    }

    private function date(): array
    {
        return ['type'=>13,  'attributes'=>['targets'=>[0], 'require'=>false, 'customAttributes'=>['questionStyle'=>1]]];
    }

    private function description(): array
    {
        return ['type'=>2, 'attributes'=>['content'=>'content']];
    }

    private function essayQuestion(): array
    {
        return ['type'=>8, 'attributes'=>['targets'=>[0], 'require'=>false,  'title'=>'EssayQuestion', 'customAttributes'=>['minLength'=>0, 'questionStyle'=>1]]];
    }

    private function feedback(): array
    {
        return ['type'=>14, 'attributes'=>['title'=>'Feedback',  'require'=>false, 'targets'=>[0]]];
    }

    private function multipleChoice(): array
    {
        return ['type'=>6, 'attributes'=>['title'=>'MultipleChoice', 'require'=>false, 'targets'=>[NominalRole::factory()->create()->id, NominalRole::factory()->create()->id], 'options'=>$this->questionFactory()]];
    }

    private function multiSelectQuestion(): array
    {
        return ['type'=>7, 'attributes'=>['title'=>'MultiSelectQuestion',  'require'=>false, 'targets'=>[0], 'options'=>$this->questionFactory()]];
    }

    private function question(): array
    {
        return ['type'=>3, 'attributes'=>['title'=>'Question', 'isText'=>false]];
    }

    private function satisfaction(): array
    {
        return ['type'=>10, 'attributes'=>['title'=>'Satisfaction',  'max'=>0, 'require'=>false, 'targets'=>[0], 'customAttributes'=>['hasNA'=>false]]];
    }

    private function signature(): array
    {
        return ['type'=>15, 'attributes'=>[]];
    }

    private function trueOrFalse(): array
    {
        return ['type'=>18, 'attributes'=>['require'=>false,  'targets'=>[0], 'options'=>$this->questionFactory()]];
    }

    private function uploadFile(): array
    {
        return ['type'=>11, 'attributes'=>['title'=>'UploadFile',  'require'=>false, 'targets'=>[0]]];
    }

    public function make(): array
    {
        $functions = ['QuestionGroup', 'FormDescribe'];

        return $this->mapMethod($functions, (array) array_rand($functions, 2));
    }

    private function mapMethod(array $methods, array $rands): array
    {
        return array_map(function ($rand) use ($methods) {
            $methodName = $methods[$rand];

            return self::$methodName();
        }, $rands);
    }

    private function QuestionTypeFactory(): array
    {
        $functions = [
            'image', 'uploadFile', 'signature', 'abilityScore',
            'autoFill', 'calculateScore', 'canvas', 'date', 'description',
            'essayQuestion', 'feedback', 'multipleChoice', 'multiSelectQuestion',
            'satisfaction', 'trueOrFalse',
        ];

        return $this->mapMethod($functions, (array) array_rand($functions, count($functions)));
    }

    private function questionFactory(): array
    {
        $questions = [];
        for ($i = 0; $i < rand(1, 10); $i++) {
            $questions[] = $this->question();
        }

        return $questions;
    }
}
