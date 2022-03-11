<?php

namespace Tests\Feature\Workflow;

use App\Models\Auth\User;
use App\Models\Workflow\IgnoreThresholdForm;
use App\Models\Workflow\ThresholdForm;
use App\Models\Workflow\ScheduleSendWorkflowForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IgnoreThresholdFormControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $this->json(
            'get',
            '/api/ignoreThresholdForm',
            [
                'origin_threshold_ids'=>[ThresholdForm::factory()->create()->id],
                'user_id'=>User::factory()->create(['deleted_at'=>null])->id,
            ]
        )->assertOk();
    }

    public function testShow()
    {
        $userId = User::factory()->create(['deleted_at'=>null])->id;
        $thresholdId = ThresholdForm::factory()->create()->id;
        IgnoreThresholdForm::factory()->state([
            'user_id'=>$userId,
            'origin_threshold_id'=>$thresholdId,
        ])->create();
        $this->get('/api/ignoreThresholdForm/showUser/'.$userId.'/'.$thresholdId)->assertOk();
    }

    public function testStore()
    {
        $thresholdId = ThresholdForm::factory()->create()->id;
        $userId = User::factory()->create(['deleted_at'=>null])->id;
        ScheduleSendWorkflowForm::factory()->state(['key_id'=>$thresholdId, 'student_id'=>$userId])->create();
        $this->post('/api/ignoreThresholdForm', [
            'origin_threshold_id'=>$thresholdId,
            'user_id'=>$userId,
        ])->assertCreated();
    }

    public function testStores()
    {
        $this->post('/api/ignoreThresholdForm/stores', [
            'origin_threshold_ids'=>[ThresholdForm::factory()->create()->id, ThresholdForm::factory()->create()->id],
            'user_id'=>User::factory()->create(['deleted_at'=>null])->id,
        ])->assertCreated();
    }

    public function testDestroy()
    {
        $ignoreThresholdFormId = IgnoreThresholdForm::factory()->create()->id;
        $this->delete('/api/ignoreThresholdForm/'.$ignoreThresholdFormId)->assertNoContent();
    }
}
