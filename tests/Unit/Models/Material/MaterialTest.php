<?php

namespace Tests\Unit\Models\Material;

use App\Models\Auth\User;
use App\Models\Material\Material;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MaterialTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testModelsCanBeInstantiated(): void
    {
        $material = Material::factory()->create();

        $this->assertTrue($material  instanceof Material);
    }

    public function testSaveToDatabase(): void
    {
        $material = Material::factory()->create();

        $this->assertIsNumeric($material->id);
    }

    public function testCanAuthUser(): void
    {
        $material = Material::factory()->create();

        $material->authUser()->attach(User::factory()->create(['deleted_at' => null])->id);

        $authId = $material->authUser->map(function ($user) {
            return $user->pivot->id;
        });

        $this->assertTrue($authId->count() > 0);
    }
}
