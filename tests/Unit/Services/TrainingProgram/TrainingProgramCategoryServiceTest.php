<?php

namespace Tests\Unit\Services\TrainingProgram;

use App\Models\Auth\User;
use App\Models\DefaultCategory;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramCategory;
use App\Models\TrainingProgram\TrainingProgramUnit;
use App\Services\TrainingProgram\TrainingProgramCategoryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

//TODO: 同步有關的測試，等系統變數完成之後需用系統變數抓正在使用的預設架構。

class TrainingProgramCategoryServiceTest extends TestCase
{
    use RefreshDatabase;

    private TrainingProgramCategoryService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new TrainingProgramCategoryService();
    }

    public function testCanGet()
    {
        $category = TrainingProgramCategory::factory()->create();

        $subject = $this->service->getById($category->id);

        $this->assertTrue($subject instanceof TrainingProgramCategory);
    }

    public function testCanInsert()
    {
        $data = TrainingProgramCategory::factory()->make()->toArray();

        $id = $this->service->create($data);

        $this->assertTrue($id > 0);
    }

    public function testCanUpdate()
    {
        $id = TrainingProgramCategory::factory()->create(['name' => 'old'])->id;

        $update = ['name' => 'new'];
        $this->service->update($id, $update);

        $result = TrainingProgramCategory::find($id);
        $this->assertSame('new', $result->name);
    }

    public function testCanDelete()
    {
        $id = TrainingProgramCategory::factory()->create()->id;

        $this->service->deleteById($id);

        $result = TrainingProgramCategory::find($id);
        $this->assertNull($result);
    }

    public function testDeleteCanThrowException()
    {
        $this->expectException(ModelNotFoundException::class);

        $id = TrainingProgramCategory::factory()->create()->id;

        $subject = $this->service->deleteById($id + 1);
    }

    public function testSyncToDefaultCategoryCanCreateCategories()
    {
        DefaultCategory::factory(2)->create();
        $program = TrainingProgram::factory()
            ->has(TrainingProgramUnit::factory(), 'programUnits')
            ->create();

        $this->service->syncToDefaultCategories(
            $program->id,
            $program->units()->first()->id,
            User::factory()->create()->id
        );

        $result = TrainingProgramCategory::where('training_program_id', $program->id)->count();

        $this->assertSame(2, $result);
    }

    public function testSyncToDefaultCategoryCanUpdateCategories()
    {
        // Arrange
        $defaultCategory = DefaultCategory::factory()->create(['name'=>'old']);
        $program = TrainingProgram::factory()
            ->has(TrainingProgramUnit::factory(), 'programUnits')
            ->create();
        $this->service->syncToDefaultCategories(
            $program->id,
            $program->units()->first()->id,
            User::factory()->create()->id
        );

        $before = TrainingProgramCategory::where('training_program_id', $program->id)->first();
        $defaultCategory->update(['name'=>'new']);

        // Act
        $this->service->syncToDefaultCategories(
            $program->id,
            $program->units()->first()->id,
            User::factory()->create()->id
        );

        // Assert
        $result = TrainingProgramCategory::where('training_program_id', $program->id);

        $this->assertSame(1, $result->count()); // 沒有新資料產生
        $this->assertSame($result->first()->id, $before->id); // 是更新舊資料
    }

    public function testSyncToDefaultCategoryCanDeleteCategories()
    {
        // Arrange
        $defaultCategory = DefaultCategory::factory(3)->create(['name'=>'old']);
        $program = TrainingProgram::factory()
            ->has(TrainingProgramUnit::factory(), 'programUnits')
            ->create();
        $this->service->syncToDefaultCategories(
            $program->id,
            $program->units()->first()->id,
            User::factory()->create()->id
        );

        $before = TrainingProgramCategory::where('training_program_id', $program->id)->first();
        $defaultCategory->first()->delete();

        // Act
        $this->service->syncToDefaultCategories(
            $program->id,
            $program->units()->first()->id,
            User::factory()->create()->id
        );

        // Assert
        $result = TrainingProgramCategory::where('training_program_id', $program->id)->count();

        $this->assertSame(2, $result); // 會刪除一筆舊資料
    }

    public function testCanCloneCategories()
    {
        $programId = TrainingProgram::factory()->create()->id;
        $categories = TrainingProgramCategory::factory(3)->create();
        $result = $this->service->cloneCategories($categories, $programId);

        collect($result)->each(function ($newId, $oldId) use ($programId, $categories) {
            $newCategory = TrainingProgramCategory::findOrFail($newId);
            $oldCategory = $categories->where('id', $oldId)->first();

            $this->assertSame($newCategory->name, $oldCategory->name);
            $this->assertSame($programId, $newCategory->training_program_id);
        });
    }

    public function testCloneCategoriesCanCloneParent()
    {
        $oldProgram = TrainingProgram::factory()->create();
        $newProgram = TrainingProgram::factory()->create();

        TrainingProgramCategory::factory(2)->has(
            TrainingProgramCategory::factory(2)->withProgram($oldProgram),
            'children'
        )->withProgram($oldProgram)->create();
        $categories = TrainingProgramCategory::where('training_program_id', $oldProgram->id)->get();

        $result = $this->service->cloneCategories($categories, $newProgram->id);

        collect($result)->each(function ($newId, $oldId) use ($newProgram, $categories) {
            $newCategory = TrainingProgramCategory::findOrFail($newId);
            $oldCategory = $categories->where('id', $oldId)->first();

            $this->assertSame($newCategory->name, $oldCategory->name);
            $this->assertSame($newProgram->id, $newCategory->training_program_id);
        });
    }
}
