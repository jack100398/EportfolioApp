<?php

namespace Tests\Unit\Services\TrainingProgram;

use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramUnit;
use App\Services\TrainingProgram\TrainingProgramUnitService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingProgramUnitServiceTest extends TestCase
{
    use RefreshDatabase;

    private TrainingProgramUnitService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new TrainingProgramUnitService();
    }

    public function testCanGet()
    {
        $programUnit = TrainingProgramUnit::factory()->create();

        $subject = $this->service->getById($programUnit->id);

        $this->assertTrue($subject instanceof TrainingProgramUnit);
    }

    public function testCanInsert()
    {
        $data = TrainingProgramUnit::factory()->make()->toArray();

        $id = $this->service->create($data);

        $this->assertTrue($id > 0);
    }

    public function testCanDelete()
    {
        $id = TrainingProgramUnit::factory()->create()->id;

        $this->service->deleteById($id);

        $result = TrainingProgramUnit::find($id);
        $this->assertNull($result);
    }

    public function testDeleteCanThrowException()
    {
        $this->expectException(ModelNotFoundException::class);

        $id = TrainingProgramUnit::factory()->create()->id;

        $subject = $this->service->deleteById($id + 1);
    }

    public function testCanCloneProgramUnits()
    {
        $program = TrainingProgram::factory()->create();
        $programUnits = TrainingProgramUnit::factory(3)->create(['training_program_id' => $program->id]);
        $newProgram = TrainingProgram::factory()->create();

        $this->service->cloneProgramUnits($programUnits, $newProgram->id);

        $newProgramUnits = TrainingProgramUnit::where('training_program_id', $newProgram->id)->get();

        $this->assertSame(3, $newProgramUnits->count());
        $this->assertSame($newProgram->id, $newProgramUnits->first()->training_program_id);
    }
}
