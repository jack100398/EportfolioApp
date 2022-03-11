<?php

namespace App\Models\Course;

use App\Models\Auth\User;
use App\Models\DefaultCategory;
use App\Models\TrainingProgram\TrainingProgramCategory;
use App\Models\TrainingProgram\TrainingProgramCourseShare;
use App\Models\Unit;
use Database\Factories\Course\CourseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory;
    use SoftDeletes;

    // public const UPDATED_AT = null;

    // public const CREATED_AT = null;
    protected $casts = ['metadata' => 'array'];

    protected $fillable = [
        'year',
        'program_category_id',
        'default_category_id',
        'course_target',
        'unit_id',
        'course_name',
        'course_remark',
        'start_at',
        'end_at',
        'signup_start_at',
        'signup_end_at',
        'course_form_send_at',
        'open_signup_for_student',
        'place',
        'course_mode',
        'is_compulsory',
        'auto_update_students',
        'created_by',
        'updated_by',
        'is_notified',
        'metadata',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(CourseMember::class, 'course_id');
    }

    public function courseTarget(): BelongsTo
    {
        return $this->belongsTo(CourseTarget::class, 'course_target');
    }

    public function trainingProgramCategory(): BelongsTo
    {
        return $this->belongsTo(TrainingProgramCategory::class, 'program_category_id');
    }

    public function defaultCategory(): BelongsTo
    {
        return $this->belongsTo(DefaultCategory::class, 'default_category_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function courseAssessment(): HasMany
    {
        return $this->hasMany(CourseAssessment::class, 'course_id');
    }

    public function nominalRoleUsers(): MorphToMany
    {
        return $this->morphToMany(
            User::class,
            'roleable',
            'nominal_role_users',
        );
    }

    public function courseShares(): BelongsToMany
    {
        return $this->belongsToMany(
            TrainingProgramCategory::class,
            TrainingProgramCourseShare::class,
            'course_id',
            'program_category_id',
            'id',
            'id'
        );
    }

    protected static function newFactory(): CourseFactory
    {
        return CourseFactory::new();
    }
}
