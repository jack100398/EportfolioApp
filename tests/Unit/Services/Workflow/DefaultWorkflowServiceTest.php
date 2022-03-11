<?php

namespace Tests\Unit\Services\Workflow;

use App\Models\NominalRole\NominalRole;
use App\Models\Unit;
use App\Models\Workflow\DefaultWorkflow;
use App\Services\Interfaces\IDefaultWorkflowService;
use App\Services\Workflow\DefaultWorkflowService;
use App\Services\Workflow\Enum\ProcessTypeEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DefaultWorkflowServiceTest extends TestCase
{
    use RefreshDatabase;

    private IDefaultWorkflowService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new DefaultWorkflowService();
    }

    public function testGetByPagination()
    {
        DefaultWorkflow::factory()->count(10)->create();
        $testData['per_page'] = 10;
        $testData['title'] = '';
        $result = $this->service->getByPagination($testData);

        $this->assertCount(10, $result);
    }

    public function testGetById()
    {
        $defaultWorkflow = DefaultWorkflow::factory()->create();
        $result = $this->service->getById($defaultWorkflow->id);
        $this->assertTrue($result instanceof DefaultWorkflow);
    }

    public function testUpdate()
    {
        $defaultWorkflow = DefaultWorkflow::factory()->create();
        $data = [
            'title' => 'test',
            'processes'=>[['role'=>NominalRole::factory()->create()->id, 'user_id'=> null, 'type'=>ProcessTypeEnum::NOTIFY]],
            'unit_id' => Unit::factory()->create()->id,
        ];
        $result = $this->service->update($defaultWorkflow, $data);
        $this->assertTrue($result === true);
    }

    public function testGetByIdsIsNotExist()
    {
        $defaultWorkflow = DefaultWorkflow::factory()->count(10)->create();
        $result = $this->service->getByIds($defaultWorkflow->pluck('id')->toArray());
        $this->assertCount(10, $result);
    }
}
