<?php

namespace App\Models\Form;

use App\Models\Workflow\Workflow;
use Database\Factories\Form\FormFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const UPDATED_AT = null;

    protected $table = 'form';

    protected $fillable =
    [
        'origin_form_id',
        'name',
        'type',
        'course_form_default_assessment',
        'form_default_workflow',
        'share_type',
        'version',
        'is_writable',
        'reviewed',
        'is_enabled',
        'questions',
        'is_sharable',
    ];

    public function formUnit(): HasMany
    {
        return $this->hasMany(FormUnit::class, 'id');
    }

    public function workflow(): HasMany
    {
        return $this->hasMany(Workflow::class, 'id');
    }

    protected static function newFactory(): FormFactory
    {
        return FormFactory::new();
    }
}
