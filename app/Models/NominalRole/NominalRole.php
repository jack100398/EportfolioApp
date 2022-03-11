<?php

namespace App\Models\NominalRole;

use App\Models\Course\Course;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramStep;
use App\Models\TrainingProgram\TrainingProgramUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NominalRole extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const TYPE_COURSE = 1;

    public const TYPE_TRAINING_PROGRAM = 2;

    public const TYPE_TRAINING_PROGRAM_UNIT = 3;

    public const TYPE_TRAINING_PROGRAM_STEP = 4;

    public const TYPES = [
        self::TYPE_COURSE => Course::class,
        self::TYPE_TRAINING_PROGRAM => TrainingProgram::class,
        self::TYPE_TRAINING_PROGRAM_UNIT => TrainingProgramUnit::class,
        self::TYPE_TRAINING_PROGRAM_STEP => TrainingProgramStep::class,
    ];

    protected $fillable = [
        'name',
        'type',
        'is_active',
    ];
}
