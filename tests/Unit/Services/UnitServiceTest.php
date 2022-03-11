<?php

namespace Tests\Unit\Services;

use App\Models\Auth\User;
use App\Models\Exam\ExamFolder;
use App\Models\Exam\ExamQuestion;
use App\Models\Unit;
use App\Services\UnitService;
use App\Services\UnitUserEnum;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitServiceTest extends TestCase
{
    use RefreshDatabase;

    private UnitService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new UnitService();
    }

    public function testCanGetManyByPagination()
    {
        Unit::factory(10)->create();
        $result = $this->service->getManyByPagination(15);
        $this->assertSame(Unit::count(), $result->total());
    }

    public function testCanDeleteById()
    {
        $id = Unit::factory()->create()->id;

        $this->service->deleteById($id);

        $result = Unit::find($id);
        $this->assertNull($result);
    }

    public function testCanUpdate()
    {
        $id = Unit::factory()->create(['name'=>'oldName'])->id;
        $this->service->update($id, ['name'=>'newName']);

        $result = Unit::find($id);
        $this->assertSame('newName', $result->name);
    }

    public function testCanGetById()
    {
        $id = Unit::factory()->create()->id;
        $result = $this->service->getById($id);
        $this->assertSame($id, $result->id);
    }

    public function testCanGetLevel()
    {
        $parentId = Unit::factory()->create(['parent_id'=>null])->id; // Level 0
        $parentId = Unit::factory()->withParent($parentId)->create()->id; // Level 1
        $unit = Unit::factory()->withParent($parentId)->create(); // Level 2

        $result = $this->service->getUnitLevel($unit->id);

        $this->assertSame(2, $result);
    }

    public function testCanAddUserToUnit()
    {
        $unit = Unit::factory()->create();
        $user = User::factory()->create(['deleted_at'=>null]);

        $result = $this->service->addUserToUnit($unit->id, $user->id, UnitUserEnum::DEFAULT);

        $this->assertTrue($result);
        $this->assertTrue($unit->refresh()->users()->count() > 0);
    }
}
