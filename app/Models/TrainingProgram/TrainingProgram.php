<?php

namespace App\Models\TrainingProgram;

use App\Models\Auth\User;
use App\Models\NominalRole\NominalRoleUser;
use App\Models\TrainingProgram\ModifiedRecord\TrainingProgramUserModifiedRecord;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingProgram extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $fillable = [
        'year',
        'unit_id',
        'occupational_class_id',
        'name',
        'start_date',
        'end_date',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function occupationalClass(): BelongsTo
    {
        return $this->belongsTo(OccupationalClass::class, 'occupational_class_id');
    }

    /**
     * 可看到該訓練計畫的單位.
     *
     * @return MorphToMany
     */
    public function authUnits(): MorphToMany
    {
        return $this->morphToMany(
            Unit::class,
            'model',
            'model_has_units',
        );
    }

    public function programUnits(): HasMany
    {
        return $this->HasMany(TrainingProgramUnit::class);
    }

    public function programUsers(): HasMany
    {
        return $this->hasMany(TrainingProgramUser::class);
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'training_program_units');
    }

    public function users(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class, 'training_program_users')
            ->withPivot('id', 'phone_number', 'group_name');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TrainingProgramAttachment::class);
    }

    /**
     * 取得主計畫.
     *
     * @return BelongsToMany
     */
    public function syncedToPrograms(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'training_program_syncs',
            'from_training_program_id',
            'to_training_program_id',
        );
    }

    /**
     * 取得子計畫.
     *
     * @return BelongsToMany
     */
    public function syncedFromPrograms(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'training_program_syncs',
            'to_training_program_id',
            'from_training_program_id',
        );
    }

    public function stepsTemplate(): HasMany
    {
        return $this->hasMany(TrainingProgramStepTemplate::class, 'training_program_id')
            ->orderBy('sequence', 'asc');
    }

    public function programUserModifiedRecords(): HasMany
    {
        return $this->hasMany(TrainingProgramUserModifiedRecord::class, 'training_program_id');
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

    public function programCategories(): HasMany
    {
        return $this->hasMany(TrainingProgramCategory::class, 'training_program_id');
    }
}
