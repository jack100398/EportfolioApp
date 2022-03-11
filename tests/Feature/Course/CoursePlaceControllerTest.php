<?php

namespace Tests\Feature\Course;

use App\Models\Auth\User;
use App\Models\Course\Course;
use App\Models\Course\CoursePlace;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CoursePlaceControllerTest extends TestCase
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

    public function testCanShowIndex()
    {
        $response = $this->get('/api/coursePlace');

        $response->assertOk();
    }

    public function testCanCreate()
    {
        $response = $this->post('/api/coursePlace', [
            'parent_id' => CoursePlace::factory()->create()->id,
            'name' => 'testing',
        ]);

        $response->assertCreated();
    }

    public function testCanUpdate()
    {
        $id = CoursePlace::factory()->create()->id;
        $response = $this->put('/api/coursePlace/'.$id, [
            'parent_id' => CoursePlace::factory()->create()->id,
            'name' => 'test',
        ]);

        $this->assertTrue(CoursePlace::find($id)->name === 'test');

        $response->assertNoContent();
    }

    public function testCanShow()
    {
        $response = $this->get('/api/coursePlace/'.CoursePlace::factory()->create()->id);
        $response->assertOk();
    }

    public function testCanDelete()
    {
        $response = $this->delete('/api/coursePlace/'.CoursePlace::factory()->create()->id);
        $response->assertNoContent();
    }
}
