<?php

namespace Tests\Unit\Services\TrainingProgram;

use App\Models\Course\Course;
use App\Models\TrainingProgram\ModifiedRecord\TrainingProgramUserModifiedRecord;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramCategory;
use App\Models\TrainingProgram\TrainingProgramSync;
use App\Models\TrainingProgram\TrainingProgramUser;
use App\Models\Unit;
use App\Services\TrainingProgram\TrainingProgramService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TrainingProgramServiceTest extends TestCase
{
    use RefreshDatabase;

    private TrainingProgramService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new TrainingProgramService();
    }

    public function testCanGet()
    {
        $program = TrainingProgram::factory()->create();

        $subject = $this->service->getById($program->id);

        $this->assertTrue($subject instanceof TrainingProgram);
    }

    public function testCanInsert()
    {
        $data = TrainingProgram::factory()->make()->toArray();

        $id = $this->service->create($data);

        $this->assertTrue($id > 0);
    }

    public function testCanUpdate()
    {
        $id = TrainingProgram::factory()->create(['name' => 'old'])->id;

        $update = ['name' => 'new'];
        $this->service->update($id, $update);

        $result = TrainingProgram::find($id);
        $this->assertSame('new', $result->name);
    }

    public function testCanDelete()
    {
        $id = TrainingProgram::factory()->create()->id;

        $this->service->deleteById($id);

        $result = TrainingProgram::find($id);
        $this->assertNull($result);
    }

    public function testDeleteCanThrowException()
    {
        $this->expectException(ModelNotFoundException::class);

        $id = TrainingProgram::factory()->create()->id;

        $subject = $this->service->deleteById($id + 1);
    }

    public function testCanGetAuthUnit()
    {
        $id = TrainingProgram::factory()
            ->has(Unit::factory(3), 'authUnits')
            ->create()->id;

        $subject = $this->service->getAuthUnit($id);

        $this->assertSame(3, $subject->count());
    }

    public function testCanCreateAuthUnit()
    {
        $program = TrainingProgram::factory()->create();
        $unit = Unit::factory(3)->create();

        $subject = $this->service->createAuthUnit($program->id, $unit->pluck('id')->toArray());

        $authUnits = $program->refresh()->authUnits;
        $this->assertSame(3, $authUnits->count());
    }

    public function testCanDeleteAuthUnit()
    {
        $program = TrainingProgram::factory()
            ->has(Unit::factory(), 'authUnits')
            ->create();
        $unit = $program->authUnits->first();

        $this->service->deleteAuthUnit($program->id, $unit->id);

        $this->assertSame(0, $program->refresh()->authUnits->count());
    }

    public function testCanGetSyncedProgram()
    {
        $program = TrainingProgram::factory()
            ->has(TrainingProgram::factory(3), 'syncedToPrograms')
            ->has(TrainingProgram::factory(2), 'syncedFromPrograms')
            ->create();

        $subject = $this->service->getSyncedProgram($program->id);

        $this->assertSame(3, count($subject['to_programs']));
        $this->assertSame(2, count($subject['from_programs']));
    }

    public function testCanSyncProgram()
    {
        $sourceProgram = TrainingProgram::factory()->create();
        $targetProgram = TrainingProgram::factory()->create();

        $this->service->syncProgram($sourceProgram->id, $targetProgram->id);

        $this->assertSame($targetProgram->id, $sourceProgram->syncedToPrograms->first()->id);
        $this->assertSame($sourceProgram->id, $targetProgram->syncedFromPrograms->first()->id);
    }

    public function testCanUnSyncProgram()
    {
        $sync = TrainingProgramSync::factory()->create();
        $sourceId = $sync->fromTrainingProgram->id;
        $targetId = $sync->toTrainingProgram->id;

        $this->service->unSyncProgram($sourceId, $targetId);

        $this->assertNull(TrainingProgramSync::where([
            'from_training_program_id' => $sourceId,
            'to_training_program_id' => $targetId,
        ])->first());
    }

    public function testCanGetUserRecord()
    {
        $program = TrainingProgram::factory()
            ->has(TrainingProgramUserModifiedRecord::factory(3), 'programUserModifiedRecords')
            ->create();

        $records = $this->service->getUserRecord($program->id);

        $this->assertSame(3, $records->count());
    }

    public function testCanGetCopyDataById()
    {
        $program = TrainingProgram::factory()->create();
        $category = TrainingProgramCategory::factory()->create(['training_program_id'=>$program->id]);
        $course = Course::factory()->create(['program_category_id'=>$category->id]);

        $result = $this->service->getCopyDataById($program->id);

        $this->assertNotNull($result->programCategories->first()->courses);
        $this->assertNotNull($result->programUnits);
    }

    public function testCanCloneTrainingProgram()
    {
        $program = TrainingProgram::factory()->create();
        $year = 999;
        $name = 'newName';
        $startDate = Carbon::createFromDate(2021, 10, 10);
        $endDate = Carbon::createFromDate(2021, 12, 12);

        $result = $this->service->cloneTrainingProgram($program, $year, $name, $startDate, $endDate);

        $this->assertNotSame($program->id, $result->id);
        $this->assertSame($name, $result->name);
        $this->assertSame($year, $result->year);
        $this->assertSame($program->occupational_class_id, $result->occupational_class_id);
        $this->assertSame($startDate->toDateString(), $result->start_date->toDateString());
        $this->assertSame($endDate->toDateString(), $result->end_date->toDateString());
    }
}
