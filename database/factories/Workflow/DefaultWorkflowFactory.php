<?php

namespace Database\Factories\Workflow;

use App\Models\Auth\User;
use App\Models\NominalRole\NominalRole;
use App\Models\Workflow\DefaultWorkflow;
use App\Services\Workflow\Enum\ProcessTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class DefaultWorkflowFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DefaultWorkflow::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->title,
            'unit_id' => $this->faker->numberBetween(0, 100),
            'process' => json_encode($this->processList()),
        ];
    }

    private function processList()
    {
        return [
            ['type'=>ProcessTypeEnum::SINGLE, 'role'=>NominalRole::factory()->create()->id, 'user_id'=>User::factory()->create(['deleted_at'=>null])->id],
            ['type'=>ProcessTypeEnum::NOTIFY, 'role'=>NominalRole::factory()->create()->id, 'user_id'=>null],
            ['type'=>ProcessTypeEnum::FILL, 'role'=>NominalRole::factory()->create()->id, 'user_id'=>null],
            ['type'=>ProcessTypeEnum::EVALUATEE, 'role'=>NominalRole::factory()->create()->id, 'user_id'=>null],
            ['type'=>ProcessTypeEnum::ANONYMOUS, 'role'=>NominalRole::factory()->create()->id, 'user_id'=>null],
        ];
    }
}
