<?php

namespace Tests\Unit\Services\NominalRole;

use App\Models\Auth\User;
use App\Models\Course\Course;
use App\Models\NominalRole\NominalRole;
use App\Models\NominalRole\NominalRoleUser;
use App\Services\NominalRole\NominalRoleUserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NominalRoleUserServiceTest extends TestCase
{
    use RefreshDatabase;

    private NominalRoleUserService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new NominalRoleUserService();
    }

    public function testCanGet()
    {
        $roleUser = NominalRoleUser::factory()->create();

        $subject = $this->service->getById($roleUser->id);

        $this->assertTrue($subject instanceof NominalRoleUser);
    }

    public function testCanInsert()
    {
        $userId = User::factory()->create()->id;
        $nominalRoleId = NominalRole::factory()->create(['type' => NominalRole::TYPE_COURSE])->id;
        $morphId = Course::factory()->create()->id;

        $id = $this->service->create($nominalRoleId, $userId, $morphId);

        $this->assertTrue($id > 0);
    }

    public function testCanDelete()
    {
        $id = NominalRoleUser::factory()->create()->id;

        $this->service->deleteById($id);

        $result = NominalRoleUser::find($id);
        $this->assertNull($result);
    }

    public function testDeleteCanThrowException()
    {
        $this->expectException(ModelNotFoundException::class);

        $id = NominalRoleUser::factory()->create()->id;

        $subject = $this->service->deleteById($id + 1);
    }

    public function testCanCloneNominalRoleUsers()
    {
        $nominalRoleUsers = NominalRoleUser::factory(3)->create([
            'roleable_type' => Course::class,
            'roleable_id' => Course::factory()->create()->id,
        ]);

        $newCourse = Course::factory()->create();

        $this->service->cloneNominalRoleUsers($nominalRoleUsers, $newCourse->id);

        $copiedRoleUsers = NominalRoleUser::where([
            'roleable_type' => Course::class,
            'roleable_id' => $newCourse->id,
        ])->get();

        $this->assertSame(3, $copiedRoleUsers->count());
    }
}
