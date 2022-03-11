<?php

namespace Tests\Feature\Course;

use App\Models\Auth\User;
use App\Models\Course\Credit;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreditControllerTest extends TestCase
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
        $response = $this->get('/api/credit');

        $response->assertOk();
    }

    public function testCanCreate()
    {
        $response = $this->post('/api/credit', [
            'year' => 110,
            'credit_name' => 'required|string',
            'credit_type' => 2,
        ]);

        $response->assertCreated();
    }

    public function testCanUpdate()
    {
        Credit::factory(10)->create();

        $response = $this->put('/api/credit/'.Credit::first()->id, [
            'year' => 111,
            'sort' => 2,
            'credit_name' => 'string',
            'credit_type' => 3,
        ]);
        $response->assertNoContent();
    }

    public function testCanShow()
    {
        Credit::factory(10)->create();

        $response = $this->get('/api/credit/'.Credit::first()->id);
        $response->assertOk();
    }

    public function testCanDelete()
    {
        Credit::factory(10)->create();

        $id = Credit::whereNotNull('parent_id')->first()->id;

        $response = $this->delete('/api/credit/'.$id);
        $response->assertNoContent();
    }

    public function testCanGetByYear()
    {
        Credit::factory(10)->create(['year' => 999]);

        $response = $this->get('api/credit/getByYear/'. 999)->assertOK();
    }
}
