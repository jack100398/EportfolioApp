<?php

namespace Database\Factories\Exam;

use App\Models\Exam\ExamQuestion;
use Faker\Generator;
use Illuminate\Container\Container;

class OptionMetadataGenerator
{
    public static function random()
    {
        /** @var \Faker\Generator */
        $faker = Container::getInstance()->make(Generator::class);

        return self::createByType($faker->randomElement(ExamQuestion::TYPES));
    }

    public static function createByType(int $type)
    {
        switch ($type) {
            case ExamQuestion::TYPE_TRUEFALSE:
                return self::createTrueFalse();
            case ExamQuestion::TYPE_CHOICE:
                return self::createChoice();
            case ExamQuestion::TYPE_FILL:
                return self::createFill();
            case ExamQuestion::TYPE_ESSAY:
                return self::createEssay();
            default:
                return self::createTrueFalse();
        }
    }

    /**
     * 是非題.
     *
     * @return array
     */
    public static function createTrueFalse()
    {
        /** @var \Faker\Generator */
        $faker = Container::getInstance()->make(Generator::class);

        return [
            'answer' => [$faker->boolean()],
            'option' => [
                0 => 'False',
                1 => 'True',
            ],
        ];
    }

    /**
     * 選擇題.
     *
     * @return array
     */
    public static function createChoice()
    {
        /** @var \Faker\Generator */
        $faker = Container::getInstance()->make(Generator::class);

        $optionCount = $faker->numberBetween(2, 5);

        $options = [];
        for ($i = 0; $i < $optionCount; $i++) {
            array_push($options, [$i => $faker->word(2)]);
        }

        return [
            'answer' => [$faker->numberBetween(0, $optionCount)],
            'option' => $options,
        ];
    }

    /**
     *  填空題.
     *
     * @return array
     */
    public static function createFill()
    {
        /** @var \Faker\Generator */
        $faker = Container::getInstance()->make(Generator::class);

        $answerCount = $faker->numberBetween(1, 3);

        $answers = [];
        for ($i = 0; $i < $answerCount; $i++) {
            array_push($answers, [$i => $faker->word()]);
        }

        return [
            'answer' => $answers,
            'option' => [],
        ];
    }

    /**
     *  簡答題沒有標準答案.
     *
     * @return array
     */
    public static function createEssay()
    {
        return [
            'answer' => [],
            'option' => [],
        ];
    }
}
