<?php

namespace Tests\Unit\Services;

use App\Models\Form\FormWriteRecord;
use App\Models\NominalRole\NominalRole;
use App\Models\Workflow\Process;
use App\Services\Form\Enum\FormWriteRecordFlagEnum;
use App\Services\Form\FormWriteRecordService;
use App\Services\Form\Interfaces\IFormWriteRecordService;
use App\Services\Interfaces\IProcessService;
use App\Services\Workflow\ProcessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormWriteRecordServiceTest extends TestCase
{
    use RefreshDatabase;

    private IFormWriteRecordService $formWriteRecordService;

    private IProcessService $processService;

    public function __construct()
    {
        parent::__construct();
        $this->formWriteRecordService = new FormWriteRecordService();
        $this->processService = new ProcessService();
    }

    public function testGetById()
    {
        $formWriteRecord = FormWriteRecord::factory()->create();
        $result = $this->formWriteRecordService->getById($formWriteRecord->id);
        $this->assertTrue($result instanceof FormWriteRecord);
    }

    public function testCreate()
    {
        $process = Process::factory()->create();

        $result = $this->formWriteRecordService->create([
            'workflow_id' => $process->workflow_id,
            'user_id' => $process->sign_by,
            'result' => ['123'],
            'flag' => FormWriteRecordFlagEnum::RESULT,
        ]);
        $this->assertTrue($result instanceof FormWriteRecord);
    }

    public function testGetResultWriteRecord()
    {
        $process = Process::factory()->create();
        $formWriteRecord = FormWriteRecord::factory()->create(['workflow_id'=>$process->workflow_id,
            'flag'=>FormWriteRecordFlagEnum::RESULT, ]);
        $result = $this->formWriteRecordService->getResultWriteRecord($process->workflow_id);
        $this->assertTrue($result->id === $formWriteRecord->id);
    }

    public function testGetTempWriteRecord()
    {
        $process = Process::factory()->create();
        $formWriteRecord = FormWriteRecord::factory()->create(
            ['flag'=>FormWriteRecordFlagEnum::TEMP, 'workflow_id'=>$process->workflow_id]
        );
        $result = $this->formWriteRecordService->getTempWriteRecord($process->workflow_id);
        $this->assertTrue($result->id === $formWriteRecord->id);
    }

    public function testDeleteById()
    {
        $process = Process::factory()->create();
        $formWriteRecord = FormWriteRecord::factory()->create(
            ['flag'=>FormWriteRecordFlagEnum::TEMP, 'workflow_id'=>$process->workflow_id]
        );
        $this->formWriteRecordService->deleteById($formWriteRecord->id);
        $result = FormWriteRecord::find($formWriteRecord->id);
        $this->assertNull($result);
    }

    /*
     * result 為 使用者填寫
     * previousResult 為 前一位使用者填寫
     * 測試 題目 targe 角色為 1 , 但 目前的使用者角色為2 , 所以不該填寫第二個欄位
     */
    public function testNoTargetSelfButWriteOneColumn()
    {
        $questions = [
            (object) [
                'type' => 5,
                'attributes'=> (object) [
                    'questions'=>  [
                        (object) [
                            'type' => 6,
                            'attributes' => (object) [
                                'targets' => [0],
                            ],
                        ],
                        (object) [
                            'type' => 7,
                            'attributes' => (object) [
                                'targets' => [NominalRole::factory()->create()->id],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = [
            [
                'text',
                'aaaaa',
            ],
        ];
        $previousResult = [
            [
                'text',
                'aaaaaaaaaaaaaa',
            ],
        ];
        $questionRequired = $this->formWriteRecordService->getRequiredWriteQuestionType(2, $questions, $previousResult, $result);
        $this->assertCount(1, $questionRequired);
    }

    /*
     * result 為 使用者填寫
     * previousResult 為 前一位使用者填寫
     * 測試 題目 required 為 true ,但目前使用者沒有填寫第一個欄位
     */
    public function testFormColumnNeedRequired()
    {
        $questions = [
            (object) [
                'type' => 5,
                'attributes'=> (object) [
                    'questions'=>  [
                        (object) [
                            'type' => 6,
                            'attributes' => (object) [
                                'targets' => [0],
                                'require' => true,
                            ],
                        ],
                        (object) [
                            'type' => 7,
                            'attributes' => (object) [
                                'targets' => [0],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = [
            [
                '',
                'aaaaa',
            ],
        ];
        $previousResult = [
            [
                '',
                'aaaaaaaaaaaaaa',
            ],
        ];
        $questionRequired = $this->formWriteRecordService->getRequiredWriteQuestionType(2, $questions, $previousResult, $result);
        $this->assertCount(1, $questionRequired);
    }

    /*
         * result 為 使用者填寫
         * previousResult 為 前一位使用者填寫
         * 測試 題目 required 為 true ,但目前使用者沒有填寫第一個欄位
         */
    public function testQuestionRequired()
    {
        $role = NominalRole::factory()->create()->id;
        $questions = [
            (object) [
                'type' => 5,
                'attributes'=> (object) [
                    'questions'=>  [
                        (object) [
                            'type' => 6,
                            'attributes' => (object) [
                                'targets' => [$role],
                                'require' => true,
                            ],
                        ],
                        (object) [
                            'type' => 7,
                            'attributes' => (object) [
                                'targets' => [0],
                                'require' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = [
            [
                '123',
                'aaaaa',
            ],
        ];
        $previousResult = [
            [
                '',
                'aaaaaaaaaaaaaa',
            ],
        ];
        $questionRequired = $this->formWriteRecordService->getRequiredWriteQuestionType($role, $questions, $previousResult, $result);
        $this->assertCount(0, $questionRequired);
    }

    public function testNotDesignateRole()
    {
        $role = NominalRole::factory()->create()->id;
        $questions = [
            (object) [
                'type' => 5,
                'attributes'=> (object) [
                    'questions'=>  [
                        (object) [
                            'type' => 6,
                            'attributes' => (object) [
                                'targets' => [$role],
                                'require' => true,
                            ],
                        ],
                        (object) [
                            'type' => 7,
                            'attributes' => (object) [
                                'targets' => [0],
                                'require' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = [
            [
                '123',
                'aaaaa',
            ],
        ];
        $previousResult = [
            [
                '123',
                'aaaaaaaaaaaaaa',
            ],
        ];
        $questionRequired = $this->formWriteRecordService->getRequiredWriteQuestionType(null, $questions, $previousResult, $result);
        $this->assertCount(0, $questionRequired);
    }

    public function testBatchDeleteByWorkflowId()
    {
        $process = Process::factory()->create();
        $formWriteRecords = FormWriteRecord::factory()->count(2)->create(
            ['flag'=>FormWriteRecordFlagEnum::TEMP, 'workflow_id'=>$process->workflow_id]
        );
        $this->formWriteRecordService->batchDeleteByWorkflowId($process->workflow_id);
        $result = collect($formWriteRecords)->map(function ($formWriteRecord) {
            if (! is_null(FormWriteRecord::find($formWriteRecord->id))) {
                return true;
            }
        })->filter(function ($result) {
            return ! is_null($result);
        });
        $this->assertCount(0, $result);
    }
}
