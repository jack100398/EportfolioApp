<?php

namespace Tests\Unit\Services\TrainingProgram;

use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramAttachment;
use App\Services\TrainingProgram\TrainingProgramAttachmentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingProgramAttachmentServiceTest extends TestCase
{
    use RefreshDatabase;

    private TrainingProgramAttachmentService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new TrainingProgramAttachmentService();
    }

    public function testCanGet()
    {
        $attachment = TrainingProgramAttachment::factory()->create();

        $subject = $this->service->getById($attachment->id);

        $this->assertTrue($subject instanceof TrainingProgramAttachment);
    }

    public function testCanInsert()
    {
        $data = TrainingProgramAttachment::factory()->make()->toArray();

        $id = $this->service->create($data);

        $this->assertTrue($id > 0);
    }

    public function testCanUpdate()
    {
        $id = TrainingProgramAttachment::factory()->create(['url' => 'old'])->id;

        $update = ['url' => 'new'];
        $this->service->update($id, $update);

        $result = TrainingProgramAttachment::find($id);
        $this->assertSame('new', $result->url);
    }

    public function testCanDelete()
    {
        $id = TrainingProgramAttachment::factory()->create()->id;

        $this->service->deleteById($id);

        $result = TrainingProgramAttachment::find($id);
        $this->assertNull($result);
    }

    public function testDeleteCanThrowException()
    {
        $this->expectException(ModelNotFoundException::class);

        $id = TrainingProgramAttachment::factory()->create()->id;

        $subject = $this->service->deleteById($id + 1);
    }

    public function testCanGetByTrainingProgramId()
    {
        $trainingProgramId = TrainingProgram::factory()->create()->id;
        TrainingProgramAttachment::factory(5)->create([
            'training_program_id' => $trainingProgramId,
        ]);

        $subject = $this->service->getByTrainingProgramId($trainingProgramId);

        $this->assertSame(5, $subject->count());
    }
}
