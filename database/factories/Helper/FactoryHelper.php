<?php

namespace Database\Factories\Helper;

class FactoryHelper
{
    /**
     * 取得隨機一筆資料庫資料，沒有的話就新增一筆.
     *
     * @param string $model
     * @return int
     */
    public static function getRandomModelId(string $model): int
    {
        $item = $model::inRandomOrder()->first();

        if (empty($item)) {
            return $model::factory()->create()->id;
        }

        return $item->id;
    }
}
