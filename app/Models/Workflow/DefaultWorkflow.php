<?php

namespace App\Models\Workflow;

use Database\Factories\Workflow\DefaultWorkflowFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DefaultWorkflow extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const UPDATED_AT = null;

    protected $table = 'default_workflow';

    protected $fillable =
    [
        'id',
        'title',
        'unit_id',
        'process',
    ];

    protected static function newFactory(): DefaultWorkflowFactory
    {
        return DefaultWorkflowFactory::new();
    }
}
