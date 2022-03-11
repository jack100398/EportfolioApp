<?php

namespace Tests\Unit\Services\Workflow;

use App\Models\TrainingProgram\TrainingProgramCategory;
use App\Models\Workflow\ThresholdForm;
use App\Services\Interfaces\IThresholdFormService;
use App\Services\Workflow\ThresholdFormService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThresholdFormServiceTest extends TestCase
{
    use RefreshDatabase;

    private IThresholdFormService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new ThresholdFormService();
    }

    public function testGetManyByPagination()
    {
        ThresholdForm::factory()->count(10)->create();
        $result = $this->service->getManyByPagination(10);
        $this->assertCount(10, $result);
    }

    public function testGetByCategoryCourseId()
    {
        ThresholdForm::get()->each(function ($thresholdForm) {
            $thresholdForm->delete();
        });
        $programCategoryId = TrainingProgramCategory::factory()->create()->id;
        ThresholdForm::factory()->count(5)->create(['program_category_id'=>$programCategoryId]);
        $result = $this->service->getByProgramCategoryId($programCategoryId);
        $this->assertCount(5, $result);
    }

    public function testGetById()
    {
        $result = $this->service->getById(ThresholdForm::factory()->create()->id);
        $this->assertTrue($result instanceof ThresholdForm);
    }

    public function testGetByIds()
    {
        $result = $this->service->getByIds([ThresholdForm::factory()->create()->id, ThresholdForm::factory()->create()->id]);
        $this->assertCount(2, $result);
    }
}
