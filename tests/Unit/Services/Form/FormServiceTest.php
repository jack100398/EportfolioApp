<?php

namespace Tests\Unit\Services\Form;

use App\Models\Form\Form;
use App\Models\Form\FormUnit;
use App\Models\Unit;
use App\Models\Workflow\Workflow;
use App\Services\Form\Enum\IsSharableEnum;
use App\Services\Form\Enum\ReviewedEnum;
use App\Services\Form\FormService;
use App\Services\Form\Interfaces\IFormService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormServiceTest extends TestCase
{
    use RefreshDatabase;

    private IFormService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new FormService();
    }

    public function testGetManyByPagination()
    {
        $forms = Form::factory()->count(10)->state(['name'=>'test', 'is_sharable'=>IsSharableEnum::ALL])->create();
        $unitId = Unit::factory()->create()->id;
        foreach ($forms as $form) {
            FormUnit::factory()->count(10)->state(['unit_id'=>$unitId, 'form_id'=>$form->id])->create();
        }

        $testData['is_sharable'] = IsSharableEnum::ALL;
        $testData['per_page'] = 10;
        $testData['name'] = 'test';
        $testData['unit_ids'] = [$unitId];
        $result = $this->service->getManyByPagination($testData);

        $this->assertCount(10, $result);
    }

    public function testGetReviewedByPagination()
    {
        Form::factory()->count(10)->state(['reviewed'=>ReviewedEnum::UNAPPROVED, 'name'=>'test'])->create();
        $testData['per_page'] = 10;
        $testData['name'] = 'test';
        $result = $this->service->getReviewedByPagination($testData);

        $this->assertCount(10, $result);
    }

    public function testGetReviewedForm()
    {
        Form::factory()->state(['reviewed'=>ReviewedEnum::UNAPPROVED])->create();
        $result = $this->service->getReviewedForm(Form::orderByDesc('id')->select('id')->first()->id);
        $this->assertNotNull($result);
    }

    public function testCreate()
    {
        $result = $this->service->create(Form::factory()->make()->toArray());

        $this->assertNotNull($result);
    }

    public function testGetWorkflowFormByPagination()
    {
        Form::factory()->count(10)->state(['is_sharable'=>IsSharableEnum::ALL])->create();
        collect(Form::orderByDesc('id')->get())->map(function ($form) {
            Workflow::factory()->state(['form_id'=>$form->id])->create();
        });
        $result = $this->service->getWorkflowFormByPagination(
            [
                'unit_ids'=>FormUnit::orderByDesc('id')->get()->pluck('unit_id')->toArray(),
                'is_sharable'=>IsSharableEnum::ALL,
                'per_page'=>10,
            ]
        );
        $this->assertCount(10, $result);
    }
}
