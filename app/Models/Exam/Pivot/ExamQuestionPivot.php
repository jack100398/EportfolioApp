<?php

namespace App\Models\Exam\Pivot;

use App\Models\Exam\Exam;
use App\Models\Exam\ExamQuestion;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ExamQuestionPivot extends Pivot
{
    public $timestamps = null;

    protected $table = 'exam_question_pivot';

    protected $fillable = [
        'exam_id',
        'question_id',
        'score',
        'sequence',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function examQuestion(): BelongsTo
    {
        return $this->belongsTo(ExamQuestion::class, 'question_id');
    }
}
