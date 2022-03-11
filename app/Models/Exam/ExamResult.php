<?php

namespace App\Models\Exam;

use Database\Factories\Exam\ExamResultFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamResult extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'metadata' => 'array',
        'is_marked' => 'boolean',
        'is_finished' => 'boolean',
    ];

    protected $fillable = [
        'exam_id',
        'user_id',
        'metadata',
        'score',
        'is_marked',
        'is_finished',
        'start_time',
        'end_time',
        'source_ip',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    protected static function newFactory(): ExamResultFactory
    {
        return ExamResultFactory::new();
    }
}
