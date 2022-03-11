<?php

namespace Tests\Unit\Services;

use App\Models\DefaultCategory;
use App\Services\DefaultCategoryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DefaultCategoryServiceTest extends TestCase
{
    use RefreshDatabase;

    private DefaultCategoryService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new DefaultCategoryService();
    }

    public function testCanGetAll()
    {
        DefaultCategory::factory(10)->create();
        $subject = $this->service->getAll();

        $this->assertSame(10, $subject->count());
    }

    public function testCanGet()
    {
        $category = DefaultCategory::factory()->create();

        $subject = $this->service->getById($category->id);

        $this->assertTrue($subject instanceof DefaultCategory);
    }

    public function testCanInsert()
    {
        $data = DefaultCategory::factory()->make()->toArray();

        $id = $this->service->create($data);

        $this->assertTrue($id > 0);
    }

    public function testCanUpdate()
    {
        $id = DefaultCategory::factory()->create(['name' => 'old'])->id;

        $update = ['name' => 'new'];
        $this->service->update($id, $update);

        $result = DefaultCategory::find($id);
        $this->assertSame('new', $result->name);
    }

    public function testCanDelete()
    {
        $id = DefaultCategory::factory()->create()->id;

        $this->service->deleteById($id);

        $result = DefaultCategory::find($id);
        $this->assertNull($result);
    }

    public function testDeleteCanThrowException()
    {
        $this->expectException(ModelNotFoundException::class);

        $id = DefaultCategory::factory()->create()->id;

        $subject = $this->service->deleteById($id + 1);
    }
}
