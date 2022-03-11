<?php

namespace App\Models\Exam;

use Database\Factories\Exam\ExamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'random_parameter' => 'array',
    ];

    protected $fillable = [
        'title',
        'description',
        'invigilator',
        'start_time',
        'end_time',
        'is_answer_visible',
        'scoring',
        'passed_score',
        'total_score',
        'question_type',
        'random_parameter',
        'limit_times',
        'answer_time',
        'created_by',
        'is_template',
        'course_id',
    ];

    public function examQuestions(): BelongsToMany
    {
        return $this->belongsToMany(
            ExamQuestion::class,
            'exam_question_pivot',
            'exam_id',
            'question_id'
        )
            ->withPivot('id', 'score', 'sequence')
            ->orderByPivot('sequence', 'asc');
    }

    public function examFolders(): BelongsToMany
    {
        return $this->belongsToMany(
            ExamFolder::class,
            'exam_folder_pivot',
            'exam_id',
            'folder_id'
        );
    }

    public function examResults(): HasMany
    {
        return $this->hasMany(ExamResult::class, 'exam_id');
    }

    protected static function newFactory(): ExamFactory
    {
        return ExamFactory::new();
    }
}
