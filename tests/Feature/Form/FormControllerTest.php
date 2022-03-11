<?php

namespace Tests\Feature\Form;

use App\Models\Form\Form;
use App\Models\Form\FormUnit;
use App\Models\Unit;
use App\Models\Workflow\DefaultWorkflow;
use App\Models\Workflow\Workflow;
use App\Services\Form\Enum\FormTypeEnum;
use App\Services\Form\Enum\IsSharableEnum;
use App\Services\Form\Enum\IsWritableEnum;
use App\Services\Form\Enum\ReviewedEnum;
use Database\Factories\Form\FormQuestionTypeFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testShowIndex()
    {
        Form::factory()->count(10)->create();
        Form::factory(10)->create();
        $this->json(
            'GET',
            '/api/form/',
            ['is_sharable'=>IsSharableEnum::NONE, 'per_page'=>10]
        )->assertOk()->assertJsonCount(13);
    }

    public function testCreateForm()
    {
        $defaultWorkflow = DefaultWorkflow::factory()->create();
        $questionFactory = new FormQuestionTypeFactory();
        Unit::factory()->count(10)->create();
        $units = Unit::take(10)->pluck('id')->toArray();
        $this->post('/api/form/', [
            'unit_ids'=>$units,
            'is_sharable'=>IsSharableEnum::NONE,
            'type'=>FormTypeEnum::GENERALLY,
            'reviewed'=>ReviewedEnum::UNAPPROVED,
            'is_writable'=>[IsWritableEnum::STUDENT],
            'questions'=>$questionFactory->make(),
            'form_default_workflow'=>[$defaultWorkflow->id],
            'course_form_default_assessment'=>$defaultWorkflow->id,
        ])->assertCreated();
    }

    public function testCreateFormNoQuestionType()
    {
        $defaultWorkflow = DefaultWorkflow::factory()->create();
        Unit::factory()->count(10)->create();
        $units = Unit::take(10)->pluck('id')->toArray();
        $this->post('/api/form/', [
            'unit_ids'=>$units,
            'is_sharable'=>IsSharableEnum::NONE,
            'type'=>FormTypeEnum::GENERALLY,
            'reviewed'=>ReviewedEnum::UNAPPROVED,
            'is_writable'=>[IsWritableEnum::STUDENT],
            'questions'=>[['title'=>'123']],
            'form_default_workflow'=>[$defaultWorkflow->id],
            'course_form_default_assessment'=>$defaultWorkflow->id,
        ])->assertNotFound();
    }

    public function testCreateFormNoQuestionTypeToTypeReturnException()
    {
        $defaultWorkflow = DefaultWorkflow::factory()->create();
        Unit::factory()->count(10)->create();
        $units = Unit::take(10)->pluck('id')->toArray();
        $result = $this->getExpectedExceptionMessageRegExp(
            $this->post('/api/form/', [
                'unit_ids'=>$units,
                'is_sharable'=>IsSharableEnum::NONE,
                'type'=>FormTypeEnum::GENERALLY,
                'reviewed'=>ReviewedEnum::UNAPPROVED,
                'is_writable'=>[IsWritableEnum::STUDENT],
                'questions'=>[['type'=>'123']],
                'form_default_workflow'=>[$defaultWorkflow->id],
                'course_form_default_assessment'=>$defaultWorkflow->id,
            ])
        );
        $this->assertNull($result);
    }

    public function testUpdateForm()
    {
        $questionFactory = new FormQuestionTypeFactory();

        Form::factory()->create();
        $form = Form::orderByDesc('id')->select('id')->take(1)->first();
        $defaultWorkflow = DefaultWorkflow::factory()->create();
        Unit::factory()->count(10)->create();
        $units = Unit::take(10)->pluck('id')->toArray();
        $this->put(
            '/api/form/'.$form->id,
            [
                'unit_ids'=>$units,
                'type'=>FormTypeEnum::GENERALLY,
                'reviewed'=>ReviewedEnum::UNAPPROVED,
                'is_sharable'=>IsSharableEnum::NONE,
                'questions'=>$questionFactory->make(),
                'form_default_workflow'=>[$defaultWorkflow->id],
                'course_form_default_assessment'=>$defaultWorkflow->id,
            ]
        )->assertOk();
    }

    public function testUpdateFormNoQuestionType()
    {
        Form::factory()->create();
        $form = Form::orderByDesc('id')->select('id')->take(1)->first();
        $defaultWorkflow = DefaultWorkflow::factory()->create();
        Unit::factory()->count(10)->create();
        $units = Unit::take(10)->pluck('id')->toArray();
        $this->put('/api/form/'.$form->id, [
            'unit_ids'=>$units,
            'is_sharable'=>IsSharableEnum::NONE,
            'type'=>FormTypeEnum::GENERALLY,
            'reviewed'=>ReviewedEnum::UNAPPROVED,
            'is_writable'=>[IsWritableEnum::STUDENT],
            'questions'=>[['title'=>'123']],
            'form_default_workflow'=>[$defaultWorkflow->id],
            'course_form_default_assessment'=>$defaultWorkflow->id,
        ])->assertNotFound();
    }

    public function testUpdateFormNoQuestionTypeToTypeReturnException()
    {
        Form::factory()->create();
        $form = Form::orderByDesc('id')->select('id')->take(1)->first();
        Unit::factory()->count(10)->create();
        $units = Unit::take(10)->pluck('id')->toArray();
        $result = $this->getExpectedExceptionMessageRegExp(
            $this->put('/api/form/'.$form->id, [
                'unit_ids'=>$units,
                'is_sharable'=>IsSharableEnum::NONE,
                'type'=>FormTypeEnum::GENERALLY,
                'reviewed'=>ReviewedEnum::UNAPPROVED,
                'is_writable'=>[IsWritableEnum::STUDENT],
                'questions'=>[['type'=>'123']],
                'form_default_workflow'=>[1, 2],
                'course_form_default_assessment'=>1,
            ])
        );
        $this->assertNull($result);
    }

    public function testUpdateFormNoFoundForm()
    {
        Unit::factory()->count(10)->create();
        $units = Unit::take(10)->pluck('id')->toArray();
        $this->put('/api/form/11111111111', [
            'unit_ids'=>$units,
            'is_sharable'=>IsSharableEnum::NONE,
            'type'=>FormTypeEnum::GENERALLY,
            'reviewed'=>ReviewedEnum::UNAPPROVED,
            'is_writable'=>[IsWritableEnum::STUDENT],
            'questions'=>[['type'=>'123']],
            'form_default_workflow'=>[1, 2],
            'course_form_default_assessment'=>1,
        ])->assertNotFound();
    }

    public function testShowForm()
    {
        Form::factory()->create();
        $form = Form::orderByDesc('id')->select('id')->take(1)->first();
        $this->get('/api/form/'.$form->id)->assertOk();
    }

    public function testDeleteForm()
    {
        Form::factory()->create();
        $form = Form::orderByDesc('id')->select('id')->take(1)->first();
        $this->delete('/api/form/'.$form->id)->assertNoContent();
    }

    public function testShowReviewedList()
    {
        Form::factory()->count(10)->state(['reviewed'=>ReviewedEnum::UNAPPROVED])->create();
        $this->json('get', '/api/form/reviewed/list/', ['per_page'=>10])->assertOk();
    }

    public function testReview()
    {
        Form::factory()->state(['reviewed'=>ReviewedEnum::UNAPPROVED])->create();
        $formIds = Form::orderByDesc('id')->select('id')->first()->pluck('id')->toArray();
        $this->patch('/api/form/reviewed/update', ['reviewed'=>ReviewedEnum::EDIT, 'form_ids' => $formIds])
        ->assertOk();
    }

    public function testUpdateFormBaseSetting()
    {
        Form::factory()->create();
        $form = Form::orderByDesc('id')->select('id')->first();
        Unit::factory()->count(10)->create();
        $units = Unit::take(10)->pluck('id')->toArray();
        $this->patch(
            '/api/form/update/base/'.$form->id,
            [
                'name' => 'test',
                'type' => FormTypeEnum::GENERALLY,
                'is_sharable' => IsSharableEnum::NONE,
                'is_writable' => [IsWritableEnum::TEACHER],
                'reviewed'=>ReviewedEnum::EDIT,
                'unit_ids' => $units,
            ]
        )
        ->assertOk();
    }

    public function testUpdateFormBaseSettingNoFoundForm()
    {
        Form::factory()->create();
        Unit::factory()->count(10)->create();
        $units = Unit::take(10)->pluck('id')->toArray();
        $this->patch(
            '/api/form/update/base/1111111',
            [
                'name' => 'test',
                'type' => FormTypeEnum::GENERALLY,
                'is_sharable' => IsSharableEnum::NONE,
                'is_writable' => [IsWritableEnum::TEACHER],
                'reviewed'=>ReviewedEnum::EDIT,
                'unit_ids' => $units,
            ]
        )
        ->assertNotFound();
    }

    public function testFormEnabled()
    {
        Form::factory()->state(['is_enabled'=>false])->create();
        $form = Form::orderByDesc('id')->select('id')->first();

        $this->patch(
            '/api/form/update/enabled/'.$form->id,
            ['is_enabled'=>true]
        )->assertNoContent();
    }

    public function testFormEnabledNoFoundForm()
    {
        Form::factory()->state(['is_enabled'=>false])->create();

        $this->patch(
            '/api/form/update/enabled/111111111',
            ['is_enabled'=>true]
        )->assertNotFound();
    }

    public function testFormCopy()
    {
        Form::factory()->create();
        $form = Form::orderByDesc('id')->select('id')->first();
        Unit::factory()->count(10)->create();
        $units = Unit::take(10)->pluck('id')->toArray();
        $this->post(
            '/api/form/store/copy',
            [
                'form_id' => $form->id,
                'name' => 'test',
                'unit_ids'=>$units,
                'is_sharable'=>IsSharableEnum::NONE,
            ]
        )->assertCreated();
    }

    public function testFormCopyNoFoundForm()
    {
        Form::factory()->create();
        Unit::factory()->count(10)->create();
        $units = Unit::take(10)->pluck('id')->toArray();
        $this->post(
            '/api/form/store/copy',
            [
                'form_id' => 111111111,
                'name' => 'test',
                'unit_ids'=>$units,
                'is_sharable'=>IsSharableEnum::NONE,
            ]
        )->assertNotFound();
    }

    public function testCanDelete()
    {
        $form = Form::factory()->create();
        $response = $this->delete('/api/form/'.$form->id);

        $response->assertNoContent();
    }

    public function testDeleteNoFoundForm()
    {
        $response = $this->delete('/api/form/11111111');

        $response->assertNotFound();
    }

    public function testSendFormList()
    {
        Form::factory()->count(10)->state(['is_sharable'=>IsSharableEnum::ALL])->create();
        collect(Form::orderByDesc('id')->get())->map(function ($form) {
            Workflow::factory()->state(['form_id'=>$form->id])->create();
        });
        collect(Form::orderByDesc('id')->get())->map(function ($form) {
            FormUnit::factory()->state(['form_id'=>$form->id])->create();
        });
        $this->post(
            'api/form/sendForm/list',
            [
                'unit_ids'=>FormUnit::orderByDesc('id')->get()->pluck('unit_id')->toArray(),
                'is_sharable'=>IsSharableEnum::ALL,
                'per_page'=>10,
            ]
        )->assertOk();
    }

    public function testSendFormListByFormName()
    {
        Form::factory()->count(10)->state(['is_sharable'=>IsSharableEnum::ALL, 'name'=>'test'])->create();
        collect(Form::orderByDesc('id')->get())->map(function ($form) {
            Workflow::factory()->state(['form_id'=>$form->id])->create();
        });

        collect(Form::orderByDesc('id')->get())->map(function ($form) {
            FormUnit::factory()->state(['form_id'=>$form->id])->create();
        });
        $this->post(
            'api/form/sendForm/list',
            [
                'unit_ids'=>FormUnit::orderByDesc('id')->get()->pluck('unit_id')->toArray(),
                'is_sharable'=>IsSharableEnum::ALL,
                'per_page'=>10,
                'name' => 'test',
            ]
        )->assertOk();
    }
}
