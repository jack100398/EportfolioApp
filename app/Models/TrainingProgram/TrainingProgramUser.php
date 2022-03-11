<?php

namespace App\Models\TrainingProgram;

use App\Models\Auth\User;
use App\Models\TrainingProgram\ModifiedRecord\TrainingProgramStepModifiedRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingProgramUser extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $timestamps = null;

    protected $fillable = [
        'training_program_id',
        'user_id',
        'phone_number',
        'group_name',
    ];

    public function trainingProgram(): BelongsTo
    {
        return $this->belongsTo(TrainingProgram::class, 'training_program_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(
            TrainingProgramStep::class,
            'program_user_id'
        );
    }

    public function programStepModifiedRecords(): HasMany
    {
        return $this->hasMany(TrainingProgramStepModifiedRecord::class, 'program_user_id');
    }
}
