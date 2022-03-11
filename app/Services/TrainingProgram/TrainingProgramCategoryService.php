<?php

namespace App\Services\TrainingProgram;

use App\Models\DefaultCategory;
use App\Models\TrainingProgram\TrainingProgramCategory;
use Illuminate\Support\Collection;

class TrainingProgramCategoryService
{
    public function create(array $data): int
    {
        return TrainingProgramCategory::create($data)->id;
    }

    public function update(int $id, array $data): bool
    {
        return TrainingProgramCategory::findOrFail($id)->update($data);
    }

    public function deleteById(int $id): bool
    {
        return TrainingProgramCategory::findOrFail($id)->delete() === true;
    }

    public function getById(int $id): TrainingProgramCategory
    {
        return TrainingProgramCategory::with('children')->findOrFail($id);
    }

    public function getByTrainingProgramAndUnitId(int $programId, int $unitId): Collection
    {
        return TrainingProgramCategory::where([
            'training_program_id' => $programId,
            'unit_id' => $unitId,
        ])->get();
    }

    /**
     * 複製計畫架構，回傳array[舊ID] = 被複製後的新ID.
     *
     * @param Collection $categories
     * @param int $programId
     *
     * @return array
     */
    public function cloneCategories(Collection $categories, int $programId): array
    {
        $map = [];
        $categories = $categories->sortByDesc('parent_id');

        $categoriesCount = $categories->count(); // 用來判斷是否只剩下異常或無用資料來跳出 while 迴圈

        while ($categories->isNotEmpty() && $categoriesCount > 0) {
            $oldCategory = $categories->shift();
            $newCategory = $oldCategory->replicate();
            $newCategory->training_program_id = $programId;

            if ($newCategory->parent_id != null) {
                if (isset($map[$oldCategory->parent_id])) {
                    $newCategory->parent_id = $map[$oldCategory->parent_id];
                } else { // 如果沒有找到 parent ，就放回去陣列裡面等待下次呼叫
                    $categories->push($oldCategory);
                    $categoriesCount--;
                    continue;
                }
            }

            $categoriesCount = $categories->count();

            $newCategory->save();
            $map[$oldCategory->id] = $newCategory->id;
        }

        return $map;
    }

    /**
     * 訓練計畫架構跟預設架構同步.
     *
     * @param int $trainingProgramId
     * @param int $unitId
     * @param int $createUserId
     *
     * @return bool
     */
    public function syncToDefaultCategories(
        int $trainingProgramId,
        int $unitId,
        int $createUserId
    ): bool {
        $defaultCategories = DefaultCategory::orderBy('id')->get();
        $categories = $this->getByTrainingProgramAndUnitId($trainingProgramId, $unitId)
            ->mapWithKeys(function ($category) {
                return [$category->id => $category];
            });

        foreach ($defaultCategories as $defaultCategory) {
            $category = $categories
                ->where('default_category_id', $defaultCategory->id)
                ->first();
            if ($category !== null) {  // 如果計畫架構已經建立則更新架構名稱
                $category->update(['name', $defaultCategory->name]);
                $categories->forget($category->id);
            } else { // 計畫架構不存在則新增架構
                $parent = $categories
                    ->where('default_category_id', $defaultCategory->parent_id)
                    ->first();
                $category = $this->createUsingDefaultCategories(
                    $trainingProgramId,
                    $unitId,
                    $parent?->id,
                    $defaultCategory,
                    $createUserId,
                );
            }
        }

        // 刪除不在預設架構內的計畫架構
        $categories->each(fn ($category) => $category->delete());

        return true;
    }

    private function createUsingDefaultCategories(
        int $trainingProgramId,
        int $unitId,
        ?int $parentId,
        DefaultCategory $defaultCategory,
        int $createUserId
    ): TrainingProgramCategory {
        return TrainingProgramCategory::create([
            'parent_id' => $parentId,
            'training_program_id' => $trainingProgramId,
            'unit_id' => $unitId,
            'default_category_id' => $defaultCategory->id,
            'is_training_item' => false,
            'name' => $defaultCategory->name,
            'sort' => 0,
            'created_by' => $createUserId,
        ]);
    }
}
