<?php

namespace Database\Factories\Form;

use App\Models\Form\Form;
use App\Services\Form\Enum\FormTypeEnum;
use App\Services\Form\Enum\IsSharableEnum;
use App\Services\Form\Enum\IsWritableEnum;
use App\Services\Form\Enum\ReviewedEnum;
use App\Services\Form\QuestionTypeFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Form::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'origin_form_id' => null,
            'name' => $this->faker->name,
            'type' => FormTypeEnum::GENERALLY,
            'course_form_default_assessment' => 1,
            'form_default_workflow' => json_encode([$this->faker->numberBetween(0, 12), $this->faker->numberBetween(0, 12)]),
            'is_writable' => json_encode(IsWritableEnum::TYPES),
            'questions' => json_encode($this->factoryQuestionType()),
            'is_sharable' => IsSharableEnum::NONE,
            'reviewed'=>ReviewedEnum::PASS,
        ];
    }

    private function factoryQuestionType(): array
    {
        $formQuestionFactory = new FormQuestionTypeFactory();

        return $this->transferQuestionGroup($formQuestionFactory->make(), new QuestionTypeFactory());
    }

    private function transferQuestionGroup(array $questionGroups, QuestionTypeFactory $questionTypeFactory): array
    {
        return collect($questionGroups)->map(function ($questionGroup) use ($questionTypeFactory) {
            if (isset($questionGroup['attributes']['questions'])) {
                $questionGroup['attributes']['questions'] =
                $this->transferQuestionType($questionGroup['attributes']['questions'], $questionTypeFactory);
            }

            return $questionTypeFactory->getQuestionType($questionGroup);
        })->reject(function ($transferQuestionType) {
            return count($transferQuestionType) === 0;
        })->toArray();
    }

    private function transferQuestionType(array $questionTypes, QuestionTypeFactory $questionTypeFactory): array
    {
        return collect($questionTypes)->map(function ($questionType) use ($questionTypeFactory) {
            return $questionTypeFactory->getQuestionType($questionType);
        })->reject(function ($transferQuestionType) {
            return count($transferQuestionType) === 0;
        })->toArray();
    }
}
