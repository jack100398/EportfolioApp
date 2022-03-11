<?php

namespace App\Models\Course;

use Database\Factories\Course\CourseHistoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseHistory extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const UPDATED_AT = null;

    public const CREATED_AT = null;

    protected $casts = ['metadata' => 'array', 'request' => 'array'];

    protected $fillable = [
        'year',
        'back_type',
        'request',
        'course_id',
        'program_category_id',
        'default_category_id',
        'unit_id',
        'course_name',
        'course_remark',
        'start_at',
        'end_at',
        'overdue_type',
        'overdue_description',
        'signup_start_at',
        'signup_end_at',
        'course_form_send_at',
        'open_signup_for_student',
        'place',
        'course_mode',
        'survey',
        'self_survey',
        'is_compulsory',
        'auto_update_students',
        'created_by',
        'updated_by',
        'is_notified',
        'metadata',
    ];

    protected static function newFactory(): CourseHistoryFactory
    {
        return CourseHistoryFactory::new();
    }
}
