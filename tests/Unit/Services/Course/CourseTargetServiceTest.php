<?php

namespace Tests\Unit\Services\Course;

use App\Models\Course\CourseTarget;
use App\Services\Course\CourseTargetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTargetServiceTest extends TestCase
{
    use RefreshDatabase;

    private CourseTargetService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new CourseTargetService();
    }

    public function testCanGet()
    {
        $id = CourseTarget::factory()->create()->id;

        $subject = $this->service->getById($id);

        $this->assertTrue($subject instanceof CourseTarget);
    }

    public function testCanInsert()
    {
        $data = CourseTarget::factory()->make()->toArray();

        $id = $this->service->create($data);

        $this->assertTrue($id > 0);
    }

    public function testCanUpdate()
    {
        $data = ['target_name' => 'new'];

        $id = CourseTarget::factory()->create(['target_name' => 'old'])->id;

        $this->service->update($id, $data);

        $targetName = CourseTarget::find($id)->target_name;

        $this->assertSame($targetName, 'new');
    }

    public function testCanDelete()
    {
        $id = CourseTarget::factory()->create()->id;
        $this->assertTrue($id > 0);

        $this->service->deleteById($id);
        $courseTarget = CourseTarget::find($id);
        $this->assertNull($courseTarget);
    }
}
