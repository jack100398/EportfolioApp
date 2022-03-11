<?php

namespace Tests\Feature\Course;

use App\Models\Auth\User;
use App\Models\Course\Course;
use App\Models\Course\CourseMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseMemberControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testCanShowIndex()
    {
        $response = $this->get('/api/courseMember');

        $response->assertOk();
    }

    public function testCreateCourseMember()
    {
        $this->createForeignData();

        $response = $this->post('/api/courseMember', [
            'course_id' => Course::first()->id,
            'user_id' => User::factory()->create()->id,
            'is_online_course' => false,
            'role' => 1,
            'updated_by' => User::factory()->create()->id,
            'state' => false,
        ]);

        $response->assertCreated();
    }

    public function testDeleteCourseMember()
    {
        CourseMember::factory(10)->create();

        $response = $this->delete('/api/courseMember/'.CourseMember::first()->id);

        $response->assertNoContent();
    }

    public function testShowCourseMember()
    {
        $response = $this->get('/api/courseMember/'.CourseMember::factory()->create()->id);

        $response->assertOk();
    }

    public function testCanUpdate()
    {
        $member = CourseMember::factory()->create();

        $response = $this->put('/api/courseMember/'.$member->id, [
            'state' => $member->state === false ? true : false,
        ]);

        $response->assertNoContent();
    }

    private function createForeignData()
    {
        Course::factory(10)->create();
        User::factory(10)->create();
    }
}
