<?php

namespace App\Services\Form\QuestionTypes;

use App\Services\File\FileService;
use App\Services\Form\QuestionTypes\Interfaces\IBaseQuestionType;

class Image implements IBaseQuestionType
{
    private int $fileId = 0;

    public function transferQuestion(array $attribute): array
    {
        $service = new FileService();
        // TODO: user id
        $this->fileId = isset($attribute['file']) ?
            $service->save('public/', $attribute['file'], $userId = 1) : 0;

        return $this->transferObjectToArray();
    }

    private function transferObjectToArray(): array
    {
        return get_object_vars($this);
    }
}
