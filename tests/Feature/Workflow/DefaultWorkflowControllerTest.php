<?php

namespace Tests\Feature\Workflow;

use App\Models\NominalRole\NominalRole;
use App\Models\Workflow\DefaultWorkflow;
use App\Services\Workflow\Enum\ProcessTypeEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DefaultWorkflowControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testShowIndex()
    {
        DefaultWorkflow::factory()->count(10)->create();
        DefaultWorkflow::factory(10)->create();
        $this->json(
            'GET',
            '/api/defaultWorkflow/',
            ['per_page'=>10]
        )->assertOk()->assertJsonCount(13);
    }

    public function testStore()
    {
        $this->post('/api/defaultWorkflow/', [
            'unit_id'=> 1,
            'title'=>'test',
            'processes'=>[['role'=>NominalRole::factory()->create()->id, 'user_id'=> null, 'type'=>ProcessTypeEnum::NOTIFY]],
        ])->assertCreated();
    }

    public function testUpdate()
    {
        $defaultWorkflow = DefaultWorkflow::factory()->create();

        $this->put('/api/defaultWorkflow/'.$defaultWorkflow->id, [
            'unit_id'=> 1,
            'title'=>'test',
            'processes'=>[['role'=>NominalRole::factory()->create()->id, 'user_id'=> null, 'type'=>ProcessTypeEnum::NOTIFY]],
        ])->assertNoContent();
    }

    public function testShowDefaultWorkflow()
    {
        $defaultWorkflow = DefaultWorkflow::factory()->create();
        $this->get('/api/defaultWorkflow/'.$defaultWorkflow->id)->assertOk();
    }

    public function testDeleteDefaultWorkflow()
    {
        $defaultWorkflow = DefaultWorkflow::factory()->create();
        $this->delete('/api/defaultWorkflow/'.$defaultWorkflow->id)->assertNoContent();
    }
}
