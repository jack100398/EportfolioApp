<?php

namespace Tests\Unit\Services\NominalRole;

use App\Models\NominalRole\NominalRole;
use App\Services\NominalRole\NominalRoleService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NominalRoleServiceTest extends TestCase
{
    use RefreshDatabase;

    private NominalRoleService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new NominalRoleService();
    }

    public function testCanGetAll()
    {
        NominalRole::factory(10)->create();
        $subject = $this->service->getAll();

        $this->assertSame(10, $subject->count());
    }

    public function testCanGet()
    {
        $role = NominalRole::factory()->create();

        $subject = $this->service->getById($role->id);

        $this->assertTrue($subject instanceof NominalRole);
    }

    public function testCanInsert()
    {
        $data = NominalRole::factory()->make()->toArray();

        $id = $this->service->create($data);

        $this->assertTrue($id > 0);
    }

    public function testCanUpdate()
    {
        $id = NominalRole::factory()->create(['name' => 'old'])->id;

        $update = ['name' => 'new'];
        $this->service->update($id, $update);

        $result = NominalRole::find($id);
        $this->assertSame('new', $result->name);
    }

    public function testCanDelete()
    {
        $id = NominalRole::factory()->create()->id;

        $this->service->deleteById($id);

        $result = NominalRole::find($id);
        $this->assertNull($result);
    }

    public function testDeleteCanThrowException()
    {
        $this->expectException(ModelNotFoundException::class);

        $id = NominalRole::factory()->create()->id;

        $subject = $this->service->deleteById($id + 1);
    }
}
