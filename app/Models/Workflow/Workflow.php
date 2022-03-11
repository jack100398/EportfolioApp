<?php

namespace App\Models\Workflow;

use App\Models\Auth\User;
use App\Models\Form\Form;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\Unit;
use Database\Factories\Workflow\WorkflowFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workflow extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const UPDATED_AT = null;

    protected $table = 'workflow';

    protected $fillable =
    [
        'id',
        'evaluatee',
        'training_program_id',
        'title',
        'form_id',
        'unit_id',
        'type',
        'data_id',
        'is_return',
        'create_by',
        'start_at',
        'end_at',
    ];

    public function processes(): HasMany
    {
        return $this->hasMany(Process::class, 'workflow_id');
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function workflowEvaluatee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluatee');
    }

    public function trainingProgram(): BelongsTo
    {
        return $this->BelongsTo(TrainingProgram::class, 'training_program_id');
    }

    public function thresholdForm(): HasOne
    {
        return $this->hasOne(ThresholdForm::class, 'id', 'data_id');
    }

    protected static function newFactory(): WorkflowFactory
    {
        return WorkflowFactory::new();
    }
}
