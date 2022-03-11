<?php

namespace App\Models\TrainingProgram;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingProgramSync extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_training_program_id',
        'to_training_program_id',
    ];

    public function fromTrainingProgram(): BelongsTo
    {
        return $this->belongsTo(TrainingProgram::class, 'from_training_program_id');
    }

    public function toTrainingProgram(): BelongsTo
    {
        return $this->belongsTo(TrainingProgram::class, 'to_training_program_id');
    }
}
