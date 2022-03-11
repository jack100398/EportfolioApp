<?php

namespace App\Models\TrainingProgram;

use App\Models\Auth\User;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingProgramStep extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $timestamps = null;

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $fillable = [
        'program_unit_id',
        'program_user_id',
        'name',
        'start_date',
        'end_date',
        'remarks',
    ];

    public function programUnit(): BelongsTo
    {
        return $this->belongsTo(TrainingProgramUnit::class, 'program_unit_id');
    }

    public function programUser(): BelongsTo
    {
        return $this->belongsTo(TrainingProgramUser::class, 'program_user_id');
    }

    public function unit(): HasOneThrough
    {
        return $this->hasOneThrough(
            Unit::class,
            TrainingProgramUnit::class,
            'id',
            'id',
            'program_unit_id',
            'unit_id'
        );
    }

    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,
            TrainingProgramUser::class,
            'id',
            'id',
            'program_user_id',
            'user_id'
        );
    }

    public function trainingProgram(): HasOneThrough
    {
        return $this->hasOneThrough(
            TrainingProgram::class,
            TrainingProgramUser::class,
            'id',
            'id',
            'program_user_id',
            'training_program_id'
        );
    }

    public function nominalRoleUsers(): MorphToMany
    {
        return $this->morphToMany(
            User::class,
            'roleable',
            'nominal_role_users',
        );
    }
}
