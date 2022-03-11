<?php

namespace Tests\Feature\Course;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Course\AssessmentType;
use App\Models\Form\Form;
use App\Models\Form\FormUnit;
use App\Models\Unit;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CourseFormAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $user = User::factory()->create();
        $user->syncRoles(Role::SUPER_ADMIN);
        Sanctum::actingAs($user, ['*']);
    }

    public function testCanShow()
    {
        $form = Form::factory()->create();

        $formUnit = FormUnit::factory()->create(['form_id' => $form->id])->unit_id;

        AssessmentType::factory()->create(['source' => $form->id]);
        $response = $this->get('/api/assessmentType/'.$formUnit);

        $response->assertOk();
    }

    public function testCanCreate()
    {
        $response = $this->post(
            '/api/assessmentType/',
            [
                'type' => 1,
                'assessment_name' => 'testing',
                'unit_id' => Unit::factory()->create()->id,
                'source' => Form::factory()->create()->id,
            ]
        );

        $response->assertCreated();
    }

    public function testUpdateRole()
    {
        $response = $this->put('/api/assessmentType/'.AssessmentType::factory()->create()->id, [
            'source' => Form::factory()->create()->id, ]);

        $response->assertNoContent();
    }

    public function testDeleteById()
    {
        $response = $this->delete('/api/assessmentType/'.AssessmentType::factory()->create()->id);

        $response->assertNoContent();
    }
}
