<?php

namespace App\Models\Workflow;

use Database\Factories\Workflow\ManualFormFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 手動發送表單.
 */
class ManualForm extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const UPDATED_AT = null;

    protected $table = 'manual_form';

    protected $fillable =
    [
        'id',
        'title',
        'training_program_id',
        'default_workflow_id',
        'form_id',
        'send_amount',
        'form_start_at',
        'form_write_at',
    ];

    protected static function newFactory(): ManualFormFactory
    {
        return ManualFormFactory::new();
    }
}
