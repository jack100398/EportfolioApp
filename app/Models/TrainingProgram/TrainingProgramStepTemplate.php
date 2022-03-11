<?php

namespace App\Models\TrainingProgram;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class TrainingProgramStepTemplate extends Model
{
    use HasFactory;

    public $timestamps = null;

    protected $fillable = [
        'training_program_id',
        'program_unit_id',
        'days',
        'sequence',
    ];

    public function trainingProgram(): BelongsTo
    {
        return $this->belongsTo(TrainingProgram::class, 'training_program_id');
    }

    public function programUnit(): BelongsTo
    {
        return $this->belongsTo(TrainingProgramUnit::class, 'program_unit_id');
    }

    public function unit(): HasOneThrough
    {
        return $this->hasOneThrough(Unit::class, TrainingProgramUnit::class);
    }
}
