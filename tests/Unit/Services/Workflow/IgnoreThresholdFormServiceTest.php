<?php

namespace Tests\Unit\Services\Workflow;

use App\Models\Auth\User;
use App\Models\Workflow\IgnoreThresholdForm;
use App\Services\Interfaces\IIgnoreThresholdFormService;
use App\Services\Workflow\IgnoreThresholdFormService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IgnoreThresholdFormServiceTest extends TestCase
{
    use RefreshDatabase;

    private IIgnoreThresholdFormService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new IgnoreThresholdFormService();
    }

    public function testGetByUserIdAndOriginThresholdId()
    {
        $userId = User::factory()->create(['deleted_at'=>null])->id;
        $ignoreThresholdForm = IgnoreThresholdForm::factory()->state(
            ['user_id'=>$userId]
        )->create();
        $result = $this->service->getByUserIdAndOriginThresholdId($userId, $ignoreThresholdForm->origin_threshold_id);
        $this->assertTrue($result instanceof IgnoreThresholdForm);
    }

    public function testGetByUserIdAndOriginThresholdIds()
    {
        $userId = User::factory()->create(['deleted_at'=>null])->id;
        IgnoreThresholdForm::factory()->state(['user_id'=>$userId])->count(10)->create();
        $result = $this->service->getByUserIdAndOriginThresholdIds(
            $userId,
            IgnoreThresholdForm::select('origin_threshold_id')
        ->get()
        ->pluck('origin_threshold_id')->toArray()
        );
        $this->assertCount(10, $result);
    }

    public function testGetById()
    {
        $result = $this->service->getById(IgnoreThresholdForm::factory()->create()->id);
        $this->assertTrue($result instanceof IgnoreThresholdForm);
    }
}
