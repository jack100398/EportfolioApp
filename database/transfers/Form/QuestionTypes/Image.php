<?php

namespace Database\Transfers\Form\QuestionTypes;

use App\Models\File;
use Database\Transfers\Form\QuestionTypes\Interfaces\IBaseQuestionType;
use Illuminate\Support\Facades\Storage;

class Image implements IBaseQuestionType
{
    private int $fileId = 0;

    public function transferQuestion(array $transferDatas): array
    {
        $this->fileId = $this->saveFile($transferDatas['question']['ques_image_name']);

        return $this->transferObjectToArray();
    }

    private function saveFile(?string $image): int
    {
        $exists = Storage::disk('local')->exists($image);
        if (! $exists) {
            return 0;
        }
        $file = new File();
        $file->name = $image;
        $file->extension = explode('.', $image)[1];
        $file->size = Storage::disk('local')->size($image);
        $file->created_by = 1;

        echo $file->save();
    }

    private function transferObjectToArray(): array
    {
        $objectToArray = get_object_vars($this);

        return $objectToArray;
    }
}
