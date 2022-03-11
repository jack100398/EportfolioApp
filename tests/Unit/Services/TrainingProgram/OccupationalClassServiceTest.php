<?php

namespace Tests\Unit\Services\TrainingProgram;

use App\Models\TrainingProgram\OccupationalClass;
use App\Services\TrainingProgram\OccupationalClassService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OccupationalClassServiceTest extends TestCase
{
    use RefreshDatabase;

    private OccupationalClassService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new OccupationalClassService();
    }

    public function testCanGet()
    {
        $class = OccupationalClass::factory()->create();

        $subject = $this->service->getById($class->id);

        $this->assertTrue($subject instanceof OccupationalClass);
    }

    public function testCanInsert()
    {
        $data = OccupationalClass::factory()->make()->toArray();

        $id = $this->service->create($data);

        $this->assertTrue($id > 0);
    }

    public function testCanUpdate()
    {
        $id = OccupationalClass::factory()->create(['name' => 'old'])->id;

        $update = ['name' => 'new'];
        $this->service->update($id, $update);

        $result = OccupationalClass::find($id);
        $this->assertSame('new', $result->name);
    }

    public function testCanDelete()
    {
        $id = OccupationalClass::factory()->create()->id;

        $this->service->deleteById($id);

        $result = OccupationalClass::find($id);
        $this->assertNull($result);
    }

    public function testDeleteCanThrowException()
    {
        $this->expectException(ModelNotFoundException::class);

        $id = OccupationalClass::factory()->create()->id;

        $subject = $this->service->deleteById($id + 1);
    }

    public function testCanGetByParentId()
    {
        $parentId = OccupationalClass::factory()->create()->id;
        OccupationalClass::factory(5)->create(['parent_id'=>$parentId]);

        $subject = $this->service->getByParentId($parentId);
        $this->assertSame(5, $subject->count());

        $subject = $this->service->getByParentId(null);
        $this->assertSame(1, $subject->count());
    }

    public function testCanGetByParentIdUsingNull()
    {
        OccupationalClass::factory(3)->create(['parent_id'=>null]);

        $subject = $this->service->getByParentId(null);
        $this->assertSame(3, $subject->count());
    }
}
