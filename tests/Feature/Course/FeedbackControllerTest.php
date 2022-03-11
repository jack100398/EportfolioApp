<?php

namespace Tests\Feature\Course;

use App\Models\Auth\User;
use App\Models\Course\Feedback;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FeedbackControllerTest extends TestCase
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

    public function testCreateCourse()
    {
        $response = $this->post('/api/feedback', [
            'comment' => 'required|string',
            'public' => false,
            'usage' => 1,
        ]);

        $response->assertCreated();
    }

    public function testUpdateCourse()
    {
        $id = $this->post('/api/feedback', [
            'comment' => 'required|string',
            'public' => false,
            'usage' => 1,
        ])->json('id');

        $response = $this->put('/api/feedback/'.$id, [
            'comment' => 'required|string',
            'public' => true,
            'usage' => 1,
        ]);

        $response->assertNoContent();

        $this->assertTrue(Feedback::find($id)->public === 1);
    }

    public function testShowCourse()
    {
        $response = $this->get('/api/feedback/'.Feedback::factory()->create()->id);
        $response->assertOk();
    }

    public function testDeleteCourse()
    {
        $feedback = Feedback::factory()->create();
        $response = $this->delete('/api/feedback/'.$feedback->id);
        $response->assertNoContent();
    }
}
