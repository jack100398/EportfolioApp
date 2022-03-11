<?php

namespace Database\Factories\Form;

use App\Models\Form\Form;
use App\Models\Form\FormWriteRecord;
use App\Models\Workflow\Process;
use App\Models\Workflow\Workflow;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormWriteRecordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FormWriteRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $process = Process::factory()->create();
        $workflow = Workflow::where('id', $process->workflow_id)->with('form')->first();
        $form = $workflow->form;

        return [
            'workflow_id' => $process->workflow_id,
            'user_id' => $process->sign_by,
            'result' => json_encode($this->generateResult($form)),
            'flag' => $this->faker->numberBetween(0, 10),
        ];
    }

    private function generateResult(Form $form): array
    {
        $factory = new FormWriteResultFactory();

        return $factory->makeResult(json_decode($form->questions));
    }
}
