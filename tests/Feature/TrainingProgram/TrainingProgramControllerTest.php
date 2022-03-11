<?php

namespace Tests\Feature\TrainingProgram;

use App\Models\Auth\User;
use App\Models\Course\Course;
use App\Models\NominalRole\NominalRoleUser;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramCategory;
use App\Models\TrainingProgram\TrainingProgramSync;
use App\Models\TrainingProgram\TrainingProgramUnit;
use App\Models\TrainingProgram\TrainingProgramUser;
use App\Models\Unit;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TrainingProgramControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
    }

    public function testCanShowIndex()
    {
        $response = $this->get('/api/trainingProgram');

        $response->assertOk();
    }

    public function testCanShow()
    {
        $trainingProgram = TrainingProgram::factory()->create();
        $response = $this->get('/api/trainingProgram/'.$trainingProgram->id);

        $response->assertOk();
    }

    public function testCanStore()
    {
        $trainingProgram = TrainingProgram::factory()->make()->toArray();
        $response = $this->post('/api/trainingProgram', $trainingProgram);

        $this->assertTrue($response->json()['id'] > 0);
    }

    public function testShowCanReturnNotFound()
    {
        $trainingProgram = TrainingProgram::factory()->create();
        $response = $this->get('/api/trainingProgram/'.$trainingProgram->id + 1);

        $response->assertNotFound();
    }

    public function testCanUpdate()
    {
        $trainingProgram = TrainingProgram::factory()->create();
        $response = $this->patch('/api/trainingProgram/'.$trainingProgram->id, [
            'name' => 'newName',
        ]);

        $response->assertNoContent();
    }

    public function testUpdateCanReturnNotFound()
    {
        $trainingProgram = TrainingProgram::factory()->create();
        $response = $this->patch('/api/trainingProgram/'.$trainingProgram->id + 1, [
            'name' => 'newName',
        ]);

        $response->assertNotFound();
    }

    public function testCanDelete()
    {
        $trainingProgram = TrainingProgram::factory()->create();
        $response = $this->delete('/api/trainingProgram/'.$trainingProgram->id);

        $response->assertNoContent();
    }

    public function testCanShowAuthUnit()
    {
        $program = TrainingProgram::factory()
            ->has(Unit::factory(3), 'authUnits')
            ->create();

        $response = $this->get("/api/trainingProgram/$program->id/authUnit");

        $response->assertOk();
    }

    public function testCanStoreAuthUnit()
    {
        $program = TrainingProgram::factory()->create();
        $data = Unit::factory(3)->create()->pluck('id')->toArray();
        $response = $this->post("/api/trainingProgram/$program->id/authUnit", $data);

        $response->assertNoContent();
    }

    public function testCanDeleteAuthUnit()
    {
        $unit = Unit::factory()->create();
        $program = TrainingProgram::factory()->create();
        $program->authUnits()->attach($unit);

        $response = $this->delete("/api/trainingProgram/$program->id/authUnit/$unit->id");

        $response->assertNoContent();
    }

    public function testDeleteAuthUnitCanDetectNotFound()
    {
        $unit = Unit::factory()->create();
        $program = TrainingProgram::factory()->create();

        $response = $this->delete("/api/trainingProgram/$program->id/authUnit/$unit->id");

        $response->assertNotFound();
    }

    public function testCanGetSyncedProgram()
    {
        $program = TrainingProgramSync::factory()->create()->fromTrainingProgram;

        $response = $this->get("/api/trainingProgram/$program->id/sync");

        $response->assertOk();
    }

    public function testCanSyncProgram()
    {
        $sourceProgram = TrainingProgram::factory()->create();
        $targetProgram = TrainingProgram::factory()->create();
        $data = [
            'from_training_program_id'=>$sourceProgram->id,
            'to_training_program_id'=>$targetProgram->id,
        ];

        $response = $this->post('api/trainingProgram/sync', $data);

        $response->assertNoContent();
    }

    public function testCanUnSyncProgram()
    {
        $sync = TrainingProgramSync::factory()->create();
        $sourceProgram = $sync->fromTrainingProgram;
        $targetProgram = $sync->toTrainingProgram;

        $response = $this->delete("api/trainingProgram/sync/$sourceProgram->id/$targetProgram->id");

        $response->assertNoContent();
    }

    public function testUnSyncProgramCanDetectNotFound()
    {
        $sourceProgram = TrainingProgram::factory()->create();
        $targetProgram = TrainingProgram::factory()->create();

        $response = $this->delete("api/trainingProgram/sync/$sourceProgram->id/$targetProgram->id");

        $response->assertNotFound();
    }

    public function testCanGetUserRecord()
    {
        $program = TrainingProgram::factory()
            ->has(TrainingProgramUser::factory(3), 'programUsers')
            ->create();

        $response = $this->get("api/trainingProgram/$program->id/userRecord");

        $response->assertOk();
    }

    public function testCanCopyTrainingProgramAndAllCategories()
    {
        $program = TrainingProgram::factory()->create();
        $programUnits = TrainingProgramUnit::factory(2)->create(['training_program_id' => $program->id]);
        $categories = TrainingProgramCategory::factory(2)->withProgram($program)->create();
        $courses = $categories->map(function ($category) {
            return Course::factory(2)->create(['program_category_id'=>$category->id]);
        });
        NominalRoleUser::factory(2)->create([
            'roleable_type'=>TrainingProgram::class,
            'roleable_id' => $program->id,
        ]);
        $programUnits->each(fn ($unit) => NominalRoleUser::factory(2)->create([
            'roleable_type'=>TrainingProgramUnit::class,
            'roleable_id' => $unit->id,
        ]));

        $data = [
            'programId' => $program->id,
            'year' => 110,
            'name' => 'newTrainingProgram',
            'startDate' => '2021-12-01',
            'endDate' => '2021-12-31',
            'doCopyCourse' => true,
        ];
        $response = $this->post('api/trainingProgram/copy', $data);

        $newProgramId = $response->json()['id'];
        $newProgram = TrainingProgram::find($newProgramId);
        $newCategories = $newProgram->programCategories;
        $newProgramUnits = $newProgram->programUnits;
        $newCourses = $newCategories->map(fn ($c) => $c->courses)->flatten();
        $newProgramNominalRoleUsers = $newProgram->nominalRoleUsers;
        $newProgramUnitNominalRoleUsers = $newProgramUnits->map(fn ($u) =>$u->nominalRoleUsers)->flatten();

        $response->assertCreated();
        $this->assertNotNull($newProgram);
        $this->assertSame(2, $newProgramNominalRoleUsers->count());
        $this->assertSame(4, $newProgramUnitNominalRoleUsers->count());
        $this->assertSame(2, $newCategories->count());
        $this->assertSame(2, $newProgramUnits->count());
        $this->assertSame(4, $newCourses->count());
    }

    public function testCopyTrainingProgramCanRespondWrongDateFormat()
    {
        $program = TrainingProgram::factory()->create();

        $data = [
            'programId' => $program->id,
            'year' => 110,
            'name' => 'newTrainingProgram',
            'startDate' => 'Wrong Date Format',
            'endDate' => '2021-12-31',
            'doCopyCourse' => true,
        ];

        $response = $this->post('api/trainingProgram/copy', $data);

        $response->assertForbidden();
    }
}
