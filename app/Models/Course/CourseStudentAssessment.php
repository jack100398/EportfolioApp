<?php

namespace App\Models\Course;

use Database\Factories\Course\CourseStudentAssessmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseStudentAssessment extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const CREATED_AT = null;

    protected $fillable = [
        'course_id',
        'course_assessment_id',
        'student_id',
        'state',
        'is_teacher_process',
        'is_student_process',
        'is_direct_pass',
    ];

    protected static function newFactory(): CourseStudentAssessmentFactory
    {
        return CourseStudentAssessmentFactory::new();
    }
}
