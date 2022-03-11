<?php

namespace App\Models\Exam;

use Database\Factories\Exam\ExamQuestionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamQuestion extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const TYPE_TRUEFALSE = 0;

    public const TYPE_CHOICE = 1;

    public const TYPE_FILL = 2;

    public const TYPE_ESSAY = 3;

    public const TYPES = [
        self::TYPE_TRUEFALSE,
        self::TYPE_CHOICE,
        self::TYPE_FILL,
        self::TYPE_ESSAY,
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    protected $fillable = [
        'folder_id',
        'context',
        'metadata',
        'answer_detail',
        'type',
    ];

    public function examFolder(): BelongsTo
    {
        return $this->belongsTo(ExamFolder::class, 'folder_id');
    }

    protected static function newFactory(): ExamQuestionFactory
    {
        return ExamQuestionFactory::new();
    }
}
