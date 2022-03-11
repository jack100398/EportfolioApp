<?php

namespace Tests\Unit\Services\Workflow;

use App\Models\Workflow\ManualForm;
use App\Models\TrainingProgram\TrainingProgram;
use App\Services\Interfaces\IManualFormService;
use App\Services\Workflow\ManualFormService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManualFormServiceTest extends TestCase
{
    use RefreshDatabase;

    private IManualFormService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new ManualFormService();
    }

    public function testGetById()
    {
        $id = ManualForm::factory()->create()->id;
        $result = $this->service->getById($id);
        $this->assertTrue($result instanceof ManualForm);
    }

    public function testGetByProgramId()
    {
        $trainingProgramId = TrainingProgram::factory()->create()->id;
        ManualForm::factory()->state(['training_program_id'=> $trainingProgramId])
            ->count(10)->create();
        $result = $this->service->getByProgramId($trainingProgramId);
        $this->assertCount(10, $result);
    }
}
