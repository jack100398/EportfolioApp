<?php

namespace Database\Factories;

use App\Models\Unit;
use Database\Factories\Helper\FactoryHelper;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Unit::class;

    private $parentId = null;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $parentId = $this->getParentId();

        return [
            'parent_id' => $parentId,
            'name' => $this->faker->company(),
            'sort' => 0,
            'display' => true,
        ];
    }

    public function withParent(int $id)
    {
        $this->parentId = $id;

        return $this;
    }

    private function getParentId()
    {
        if ($this->parentId === null) {
            return $this->faker->boolean()
                ? FactoryHelper::getRandomModelId($this->model)
                : null;
        }

        return $this->parentId;
    }
}
