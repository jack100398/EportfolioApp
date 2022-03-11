<?php

namespace App\Models\TrainingProgram;

use App\Models\NominalRole\NominalRoleUser;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingProgramUnit extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $timestamps = null;

    protected $fillable = [
        'training_program_id',
        'unit_id',
    ];

    public function trainingProgram(): BelongsTo
    {
        return $this->belongsTo(TrainingProgram::class, 'training_program_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function nominalRoleUsers(): MorphMany
    {
        return $this->morphMany(
            NominalRoleUser::class,
            'nominal_role_users',
            'roleable_type',
            'roleable_id'
        );
    }
}
