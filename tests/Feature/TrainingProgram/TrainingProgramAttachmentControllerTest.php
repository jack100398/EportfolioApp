<?php

namespace Tests\Feature\TrainingProgram;

use App\Models\Auth\User;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramAttachment;
use App\Models\TrainingProgram\TrainingProgramSync;
use App\Models\TrainingProgram\TrainingProgramUser;
use App\Models\Unit;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TrainingProgramAttachmentControllerTest extends TestCase
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

    public function testCanShow()
    {
        $attachment = TrainingProgramAttachment::factory()->create();
        $response = $this->get('/api/trainingProgram/attachment/'.$attachment->id);

        $response->assertOk();
    }

    public function testCanStore()
    {
        $attachment = TrainingProgramAttachment::factory()->make()->toArray();
        $response = $this->post('/api/trainingProgram/attachment', $attachment);

        $this->assertTrue($response->json()['id'] > 0);
    }

    public function testShowCanReturnNotFound()
    {
        $attachment = TrainingProgramAttachment::factory()->create();
        $response = $this->get('/api/trainingProgram/attachment/'.$attachment->id + 1);

        $response->assertNotFound();
    }

    public function testCanUpdate()
    {
        $attachment = TrainingProgramAttachment::factory()->create();
        $response = $this->patch('/api/trainingProgram/attachment/'.$attachment->id, [
            'name' => 'newName',
        ]);

        $response->assertNoContent();
    }

    public function testUpdateCanReturnNotFound()
    {
        $attachment = TrainingProgramAttachment::factory()->create();
        $response = $this->patch('/api/trainingProgram/attachment/'.$attachment->id + 1, [
            'name' => 'newName',
        ]);

        $response->assertNotFound();
    }

    public function testCanDelete()
    {
        $attachment = TrainingProgramAttachment::factory()->create();
        $response = $this->delete('/api/trainingProgram/attachment/'.$attachment->id);

        $response->assertNoContent();
    }

    public function testCanGetByTrainingProgramId()
    {
        $program = TrainingProgram::factory()
            ->has(TrainingProgramAttachment::factory(3), 'attachments')
            ->create();
        $response = $this->get("/api/trainingProgram/$program->id/attachment");

        $response->assertOk();
    }
}
