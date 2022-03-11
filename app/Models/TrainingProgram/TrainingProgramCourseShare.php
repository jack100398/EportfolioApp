<?php

namespace App\Models\TrainingProgram;

use App\Models\Course\Course;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingProgramCourseShare extends Model
{
    public $timestamps = null;

    protected $fillable = [
        'course_id',
        'program_category_id',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function programCategory(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'program_category_id');
    }
}
