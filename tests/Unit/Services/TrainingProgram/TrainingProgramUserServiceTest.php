<?php

namespace Tests\Unit\Services\TrainingProgram;

use App\Models\Auth\User;
use App\Models\TrainingProgram\ModifiedRecord\TrainingProgramUserModifiedRecord;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramSync;
use App\Models\TrainingProgram\TrainingProgramUser;
use App\Services\TrainingProgram\TrainingProgramUserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingProgramUserServiceTest extends TestCase
{
    use RefreshDatabase;

    private TrainingProgramUserService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new TrainingProgramUserService();
    }

    public function testCanGet()
    {
        $programUser = TrainingProgramUser::factory()->create();

        $subject = $this->service->getById($programUser->id);

        $this->assertTrue($subject instanceof TrainingProgramUser);
    }

    public function testCanInsert()
    {
        $data = TrainingProgramUser::factory()->make()->toArray();

        $id = $this->service->create($data);

        $this->assertTrue($id > 0);
    }

    public function testCanUpdate()
    {
        $id = TrainingProgramUser::factory()->create(['phone_number' => 'old'])->id;

        $update = ['phone_number' => 'new'];
        $this->service->update($id, $update);

        $result = TrainingProgramUser::find($id);
        $this->assertSame('new', $result->phone_number);
    }

    public function testCanDelete()
    {
        $id = TrainingProgramUser::factory()->create()->id;

        $this->service->deleteById($id);

        $result = TrainingProgramUser::find($id);
        $this->assertNull($result);
    }

    public function testDeleteCanThrowException()
    {
        $this->expectException(ModelNotFoundException::class);

        $id = TrainingProgramUser::factory()->create()->id;

        $subject = $this->service->deleteById($id + 1);
    }

    public function testCreateWillCreateRecord()
    {
        $data = TrainingProgramUser::factory()->make([
            'user_id' => User::factory()->create(['deleted_at'=> null])->id,
        ])->toArray();

        $id = $this->service->create($data);

        $programUser = TrainingProgramUser::find($id);
        $program = $programUser->trainingProgram;
        $record = $program->programUserModifiedRecords()->where([
            'user_id'=> $programUser->user_id,
            'action'=> TrainingProgramUserModifiedRecord::CREATED,
        ])->first();

        $this->assertNotNull($record);
    }

    public function testUpdateWillCreateRecord()
    {
        $programUser = TrainingProgramUser::factory()->create();

        $this->service->update($programUser->id, ['phone_number' => '123abc']);

        $program = $programUser->trainingProgram;
        $record = $program->programUserModifiedRecords()->where([
            'user_id'=> $programUser->user_id,
            'action'=> TrainingProgramUserModifiedRecord::UPDATED,
        ])->first();

        $this->assertNotNull($record);
    }

    public function testDeleteWillCreateRecord()
    {
        $programUser = TrainingProgramUser::factory()->create();

        $this->service->deleteById($programUser->id);

        $program = $programUser->trainingProgram;
        $record = $program->programUserModifiedRecords()->where([
            'user_id'=> $programUser->user_id,
            'action'=> TrainingProgramUserModifiedRecord::DELETED,
        ])->first();

        $this->assertNotNull($record);
    }

    public function testInsertWillSyncTrainingProgram()
    {
        // Arrange
        $fromProgram = TrainingProgram::factory()->create();
        $toProgram = TrainingProgram::factory()->create();
        TrainingProgramSync::create([
            'from_training_program_id' => $fromProgram->id,
            'to_training_program_id' => $toProgram->id,
        ]);

        $user = User::factory()->create();
        // Act
        $this->service->create([
            'training_program_id' => $fromProgram->id,
            'user_id' => $user->id,
            'phone_number' => '',
            'group_name' => '',
        ]);

        // Assert
        $this->assertNotNull(TrainingProgramUser::where([
            'training_program_id' => $toProgram->id,
            'user_id' => $user->id,
        ])->first());
    }

    public function testInsertWontSyncWhenTargetProgramAlreadyHaveTheUser()
    {
        // Arrange
        $fromProgram = TrainingProgram::factory()->create();
        $toProgram = TrainingProgram::factory()->create();
        TrainingProgramSync::create([
            'from_training_program_id' => $fromProgram->id,
            'to_training_program_id' => $toProgram->id,
        ]);

        $user = User::factory()->create();

        TrainingProgramUser::create([
            'training_program_id' => $toProgram->id,
            'user_id'=> $user->id,
            'phone_number' => '',
            'group_name' => '',
        ]);

        // Act
        $this->service->create([
            'training_program_id' => $fromProgram->id,
            'user_id' => $user->id,
            'phone_number' => '',
            'group_name' => '',
        ]);

        $count = TrainingProgramUser::where([
            'training_program_id' => $toProgram->id,
            'user_id' => $user->id,
        ])->count();
        // Assert
        $this->assertSame(1, $count);
    }
}
