<?php

namespace Database\Factories\Workflow;

use App\Models\Auth\User;
use App\Models\NominalRole\NominalRole;
use App\Models\Workflow\Process;
use App\Models\Workflow\Workflow;
use App\Services\Workflow\Enum\ProcessStateEnum;
use App\Services\Workflow\Enum\ProcessTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProcessFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Process::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'workflow_id'=>Workflow::factory()->create()->id,
            'is_default'=>true,
            'type'=>ProcessTypeEnum::SINGLE,
            'error_status'=>0,
            'state'=>ProcessStateEnum::NO_START,
            'sign_by'=>User::factory()->create(['deleted_at'=>null])->id,
            'role' => NominalRole::factory()->create()->id,
            'opinion' => 'test',
        ];
    }
}
