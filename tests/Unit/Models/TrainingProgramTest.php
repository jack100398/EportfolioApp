<?php

namespace Tests\Unit\Models;

use App\Models\Auth\User;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramAttachment;
use App\Models\TrainingProgram\TrainingProgramStep;
use App\Models\TrainingProgram\TrainingProgramUnit;
use App\Models\TrainingProgram\TrainingProgramUser;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingProgramTest extends TestCase
{
    use RefreshDatabase;

    public function testCanCreate()
    {
        $trainingProgram = TrainingProgram::factory()->create();

        $this->assertInstanceOf(TrainingProgram::class, $trainingProgram);
    }

    public function testCanHaveProgramUnit()
    {
        $trainingProgram = TrainingProgram::factory()
            ->has(TrainingProgramUnit::factory(2), 'programUnits')
            ->create();

        $this->assertSame(2, $trainingProgram->programUnits->count());
    }

    public function testCanHaveProgramUser()
    {
        $trainingProgram = TrainingProgram::factory()
            ->has(TrainingProgramUser::factory(2), 'programUsers')
            ->create();

        $this->assertSame(2, $trainingProgram->programUsers->count());
    }

    public function testCanAccessUnitThroughProgramUnit()
    {
        $trainingProgram = TrainingProgram::factory()
            ->has(TrainingProgramUnit::factory(2), 'programUnits')
            ->create();
        $unit = $trainingProgram->units()->first();

        $this->assertInstanceOf(Unit::class, $unit);
    }

    public function testCanAccessUserThroughProgramUser()
    {
        $trainingProgram = TrainingProgram::factory()->create();
        $user = User::factory()->create(['deleted_at'=>null]);
        $programUser = TrainingProgramUser::factory()->create([
            'user_id'=>$user->id,
            'training_program_id'=>$trainingProgram->id,
        ]);

        $subject = $trainingProgram->users()->first();

        $this->assertInstanceOf(User::class, $subject);
    }

    public function testCanGiveAuthToOtherUnits()
    {
        $trainingProgram = TrainingProgram::factory()->create();
        $trainingProgram->authUnits()->attach(Unit::factory()->create());

        $authUnit = $trainingProgram->authUnits()->first();

        $this->assertInstanceOf(Unit::class, $authUnit);
    }

    public function testCanHaveAttachments()
    {
        $trainingProgram = TrainingProgram::factory()
            ->has(TrainingProgramAttachment::factory(), 'attachments')
            ->create();

        $attachment = $trainingProgram->attachments()->first();

        $this->assertInstanceOf(TrainingProgramAttachment::class, $attachment);
    }

    public function testProgramStepCanAccessTrainingProgram()
    {
        $programStep = TrainingProgramStep::factory()->create();
        $trainingProgram = $programStep->trainingProgram;

        $this->assertInstanceOf(TrainingProgram::class, $trainingProgram);
    }
}
