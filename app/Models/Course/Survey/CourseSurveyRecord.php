<?php

namespace App\Models\Course\Survey;

use Database\Factories\Course\Survey\CourseSurveyRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseSurveyRecord extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const UPDATED_AT = null;

    // public const CREATED_AT = null;
    protected $casts = ['metadata' => 'array'];

    protected $fillable = [
        'answered_by',
        'course_survey_id',
        'role_type',
        'metadata',
    ];

    protected static function newFactory(): CourseSurveyRecordFactory
    {
        return CourseSurveyRecordFactory::new();
    }
}
