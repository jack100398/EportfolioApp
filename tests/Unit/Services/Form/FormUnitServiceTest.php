<?php

namespace Tests\Unit\Services\Form;

use App\Models\Form\Form;
use App\Models\Form\FormUnit;
use App\Models\Unit;
use App\Services\Form\FormUnitService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormUnitServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function initMock($class)
    {
        $mock = \Mockery::mock($class);
        $this->app->instance($class, $mock);

        return $mock;
    }

    public function testShowMany()
    {
        $testData = FormUnit::factory(3)->make();
        $service = $this->initMock(FormUnitService::class);

        $service->shouldReceive('showMany')
            ->once()
            ->with(1, 1)
            ->andReturn($testData);

        $res = $service->showMany(1, 1);
        $this->assertCount(3, $res);
    }

    public function testGetByFormId()
    {
        $formTestData = Form::factory()->make();
        $formTestData->save();

        $formUnitTestData = new FormUnit();
        $formUnitService = new FormUnitService();
        $formUnitTestData->form_id = $formTestData->id;
        $formUnitTestData->unit_id = Unit::factory()->create()->id;
        $formUnitTestData->save();

        $result = $formUnitService->getByFormId($formTestData->id);
        $this->assertCount(1, $result);
    }
}
