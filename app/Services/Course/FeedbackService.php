<?php

namespace App\Services\Course;

use App\Models\Course\Feedback;

class FeedbackService
{
    public function create(array $data, int $creator): int
    {
        $data['created_by'] = $creator;

        $data['comment'] = $this->comment_format($data['comment']);

        return Feedback::create($data)->id;
    }

    public function update(int $id, array $data): bool
    {
        $data['comment'] = $this->comment_format($data['comment']);

        return Feedback::findOrFail($id)->update($data) === true;
    }

    public function delete(int $id): bool
    {
        return Feedback::findOrFail($id)->delete() === true;
    }

    public function getById(int $id): Feedback
    {
        return Feedback::findOrFail($id);
    }

    private function comment_format(string $comment): string
    {
        $comment = strip_tags($comment);
        $comment = str_replace(["\xc2\xa0", ' '], '', $comment);

        return $comment;
    }
}
