<?php

namespace Database\Factories\Exam;

use App\Models\Exam\ExamFolder;
use App\Models\Exam\ExamQuestion;
use Database\Factories\Helper\FactoryHelper;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamQuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExamQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $type = $this->faker->randomElement(ExamQuestion::TYPES);
        $metadata = OptionMetadataGenerator::createByType($type);

        return [
            'folder_id'     => FactoryHelper::getRandomModelId(ExamFolder::class),
            'context'       => $this->faker->text(50),
            'metadata'      => $metadata,
            'answer_detail' => $this->faker->text(100),
            'type'          => $type,
        ];
    }

    public function withType(int $type)
    {
        return $this->state(function (array $attributes) use ($type) {
            $metadata = OptionMetadataGenerator::createByType($type);

            return [
                'metadata' => $metadata,
                'type' => $type,
            ];
        });
    }
}
