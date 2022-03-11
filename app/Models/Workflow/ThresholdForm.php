<?php

namespace App\Models\Workflow;

use Database\Factories\Workflow\ThresholdFormFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThresholdForm extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const UPDATED_AT = null;

    protected $table = 'threshold_form';

    protected $fillable =
    [
        'id',
        'program_category_id',
        'default_workflow_id',
        'origin_threshold_id',
        'form_id',
        'send_amount',
        'form_start_at',
        'form_write_at',
    ];

    protected static function newFactory(): ThresholdFormFactory
    {
        return ThresholdFormFactory::new();
    }
}
