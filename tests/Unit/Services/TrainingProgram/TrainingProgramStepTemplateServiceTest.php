<?php

namespace Tests\Unit\Services\TrainingProgram;

use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramStep;
use App\Models\TrainingProgram\TrainingProgramStepTemplate;
use App\Models\TrainingProgram\TrainingProgramSync;
use App\Models\TrainingProgram\TrainingProgramUnit;
use App\Models\TrainingProgram\TrainingProgramUser;
use App\Services\TrainingProgram\TrainingProgramStepTemplateService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingProgramStepTemplateServiceTest extends TestCase
{
    use RefreshDatabase;

    private TrainingProgramStepTemplateService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new TrainingProgramStepTemplateService();
    }

    public function testCanGet()
    {
        $stepTemplate = TrainingProgramStepTemplate::factory()->create();

        $subject = $this->service->getById($stepTemplate->id);

        $this->assertTrue($subject instanceof TrainingProgramStepTemplate);
    }

    public function testCanInsert()
    {
        $data = TrainingProgramStepTemplate::factory()->make()->toArray();

        $id = $this->service->create($data);

        $this->assertTrue($id > 0);
    }

    public function testCanUpdate()
    {
        $id = TrainingProgramStepTemplate::factory()->create(['days' => 1])->id;

        $update = ['days' => 2];
        $this->service->update($id, $update);

        $result = TrainingProgramStepTemplate::find($id);
        $this->assertSame(2, $result->days);
    }

    public function testCanDelete()
    {
        $id = TrainingProgramStepTemplate::factory()->create()->id;

        $this->service->deleteById($id);

        $result = TrainingProgramStepTemplate::find($id);
        $this->assertNull($result);
    }

    public function testDeleteCanThrowException()
    {
        $this->expectException(ModelNotFoundException::class);

        $id = TrainingProgramStepTemplate::factory()->create()->id;

        $subject = $this->service->deleteById($id + 1);
    }

    public function testCanGetByTrainingProgramId()
    {
        $trainingProgramId = TrainingProgram::factory()->create()->id;
        TrainingProgramStepTemplate::factory(5)->create([
            'training_program_id' => $trainingProgramId,
        ]);

        $subject = $this->service->getByTrainingProgramId($trainingProgramId);

        $this->assertSame(5, $subject->count());
    }
}
