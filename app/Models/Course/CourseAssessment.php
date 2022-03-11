<?php

namespace App\Models\Course;

use Database\Factories\Course\CourseAssessmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseAssessment extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'course_id',
        'assessment_id',
        'data',
    ];

    protected static function newFactory(): CourseAssessmentFactory
    {
        return CourseAssessmentFactory::new();
    }
}
