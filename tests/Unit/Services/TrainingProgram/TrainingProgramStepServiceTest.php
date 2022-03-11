<?php

namespace Tests\Unit\Services\TrainingProgram;

use App\Models\Auth\User;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramStep;
use App\Models\TrainingProgram\TrainingProgramStepTemplate;
use App\Models\TrainingProgram\TrainingProgramSync;
use App\Models\TrainingProgram\TrainingProgramUnit;
use App\Models\TrainingProgram\TrainingProgramUser;
use App\Services\TrainingProgram\TrainingProgramStepService;
use App\Services\TrainingProgram\TrainingProgramUserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingProgramStepServiceTest extends TestCase
{
    use RefreshDatabase;

    private TrainingProgramStepService $service;

    private TrainingProgramUserService $userService;

    public function __construct()
    {
        parent::__construct();
        $this->service = new TrainingProgramStepService();
        $this->userService = new TrainingProgramUserService();
    }

    public function testCanGet()
    {
        $step = TrainingProgramStep::factory()->create();

        $subject = $this->service->getById($step->id);

        $this->assertTrue($subject instanceof TrainingProgramStep);
    }

    public function testCanInsert()
    {
        $data = TrainingProgramStep::factory()->make()->toArray();

        $id = $this->service->create($data);

        $this->assertTrue($id > 0);
    }

    public function testCanUpdate()
    {
        $id = TrainingProgramStep::factory()->create(['name' => 'old'])->id;

        $update = ['name' => 'new'];
        $this->service->update($id, $update);

        $result = TrainingProgramStep::find($id);
        $this->assertSame('new', $result->name);
    }

    public function testCanDelete()
    {
        $id = TrainingProgramStep::factory()->create()->id;

        $this->service->deleteById($id);

        $result = TrainingProgramStep::find($id);
        $this->assertNull($result);
    }

    public function testDeleteCanThrowException()
    {
        $this->expectException(ModelNotFoundException::class);

        $id = TrainingProgramStep::factory()->create()->id;

        $subject = $this->service->deleteById($id + 1);
    }

    public function testInsertWillSyncTrainingProgramSteps()
    {
        // Arrange 建立同步計畫和一個學生
        $sourceProgram = TrainingProgram::factory()->create();
        $targetProgram = TrainingProgram::factory()->create();

        TrainingProgramStepTemplate::factory(3)->create(['training_program_id'=>$targetProgram->id]);
        TrainingProgramSync::create([
            'from_training_program_id' => $sourceProgram->id,
            'to_training_program_id' => $targetProgram->id,
        ]);

        $id = $this->userService->create(
            TrainingProgramUser::factory()
                ->make([
                    'training_program_id'=>$sourceProgram->id,
                    'user_id'=>User::factory()->create(['deleted_at'=>null])->id,
                ])
                ->toArray()
        );
        $sourceProgramUser = TrainingProgramUser::find($id);

        // Act
        $this->service->create([
            'program_user_id' => $sourceProgramUser->id,
            'program_unit_id' => TrainingProgramUnit::factory()->create()->id,
            'name' => '',
            'remarks' => '',
            'start_date'=>now(),
            'end_date'=>now()->addDay(),
        ]);

        // Assert 同步計畫有跟著建立站別
        $syncedProgramUser = TrainingProgramUser::where([
            'training_program_id' => $targetProgram->id,
            'user_id' => $sourceProgramUser->user->id,
        ])->first();

        $syncedStepCount = TrainingProgramStep::where([
            'program_user_id' => $syncedProgramUser->id,
        ])->count();

        $this->assertSame(3, $syncedStepCount);
    }

    public function testInsertWontSyncStepsWhenSourceProgramAlreadyHaveSteps()
    {
        // Arrange 建立同步計畫和學生
        $fromProgram = TrainingProgram::factory()->create();
        $toProgram = TrainingProgram::factory()->create();

        $fromProgramUser = TrainingProgramUser::factory()->create([
            'training_program_id'=>$fromProgram->id,
            'user_id'=>User::factory()->create(['deleted_at'=> null])->id,
        ]);
        $toProgramUser = TrainingProgramUser::factory()->create([
            'training_program_id'=>$toProgram->id,
            'user_id' => $fromProgramUser->user_id,
        ]);
        // 提前建立來源計畫站別
        TrainingProgramStep::factory()->create(['program_user_id' => $fromProgramUser->id]);

        TrainingProgramSync::create([
            'from_training_program_id' => $fromProgram->id,
            'to_training_program_id' => $toProgram->id,
        ]);

        // 建立子計畫站別樣板
        TrainingProgramStepTemplate::factory(3)->create([
            'training_program_id'=>$toProgram->id,
        ]);

        // Act 建立學生站別
        $this->service->create([
            'program_user_id' => $fromProgramUser->id,
            'program_unit_id' => TrainingProgramUnit::factory()->create()->id,
            'name' => '',
            'remarks' => '',
            'start_date'=>now()->addDays(2),
            'end_date'=>now()->addDays(3),
        ]);

        // Assert 同步計畫有站別後不會再建立站別
        $syncedProgramUser = TrainingProgramUser::where([
            'training_program_id' => $toProgram->id,
            'user_id' => $fromProgramUser->user->id,
        ])->first();

        $syncedStepCount = TrainingProgramStep::where([
            'program_user_id' => $syncedProgramUser->id,
        ])->count();

        $this->assertSame(0, $syncedStepCount);
    }

    public function testInsertWontSyncStepsWhenTargetProgramAlreadyHaveSteps()
    {
        // Arrange 建立同步計畫和學生
        $fromProgram = TrainingProgram::factory()->create();
        $toProgram = TrainingProgram::factory()->create();

        $fromProgramUser = TrainingProgramUser::factory()->create(['training_program_id'=>$fromProgram->id]);
        $toProgramUser = TrainingProgramUser::factory()->create([
            'training_program_id'=>$toProgram->id,
            'user_id' => $fromProgramUser->user_id,
        ]);

        // 提前建立子計畫站別
        TrainingProgramStep::factory()->create(['program_user_id' => $toProgramUser->id]);

        TrainingProgramSync::create([
            'from_training_program_id' => $fromProgram->id,
            'to_training_program_id' => $toProgram->id,
        ]);

        // 建立子計畫站別樣板
        TrainingProgramStepTemplate::factory(3)->create([
            'training_program_id'=>$toProgram->id,
        ]);

        // Act 建立學生站別
        $this->service->create([
            'program_user_id' => $fromProgramUser->id,
            'program_unit_id' => TrainingProgramUnit::factory()->create()->id,
            'name' => '',
            'remarks' => '',
            'start_date'=>now()->addDays(2),
            'end_date'=>now()->addDays(3),
        ]);

        // Assert 同步計畫有站別後不會再建立站別
        $syncedProgramUser = TrainingProgramUser::where([
            'training_program_id' => $toProgram->id,
            'user_id' => $fromProgramUser->user_id,
        ])->first();

        $syncedStepCount = TrainingProgramStep::where([
            'program_user_id' => $syncedProgramUser->id,
        ])->count();

        $this->assertSame(1, $syncedStepCount);
    }

    public function testCanGetUserSteps()
    {
        $userId = User::factory()->create(['deleted_at'=>null])->id;
        $programUserId = TrainingProgramUser::factory()->create(['user_id' => $userId]);
        TrainingProgramStep::factory(2)->create(['program_user_id' => $programUserId]);
        TrainingProgramStep::factory(2)->create(); // 不同user的站別

        $result = $this->service->getUserSteps($userId);

        $this->assertSame(2, $result->count());
    }
}
