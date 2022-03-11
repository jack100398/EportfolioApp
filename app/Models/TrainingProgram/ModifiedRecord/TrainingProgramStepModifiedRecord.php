<?php

namespace App\Models\TrainingProgram\ModifiedRecord;

use App\Models\Auth\User;
use App\Models\TrainingProgram\TrainingProgram;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingProgramStepModifiedRecord extends Model
{
    use HasFactory;

    public const CREATED = 0;

    public const UPDATED = 1;

    public const DELETED = 2;

    public const ACTIONS = [
        self::CREATED,
        self::UPDATED,
        self::DELETED,
    ];

    protected $fillable = [
        'action',
        'program_user_id',
        'program_unit_id',
        'name',
        'start_date',
        'end_date',
        'remarks',
        'created_by',
    ];

    public function trainingProgram(): BelongsTo
    {
        return $this->belongsTo(TrainingProgram::class, 'training_program_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
