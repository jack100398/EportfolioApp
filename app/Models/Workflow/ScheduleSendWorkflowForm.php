<?php

namespace App\Models\Workflow;

use Database\Factories\Workflow\ScheduleSendWorkflowFormFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleSendWorkflowForm extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'schedule_send_workflow_form';

    protected $fillable = [
        'key_id',
        'title',
        'unit_id',
        'type',
        'start_at',
        'end_at',
        'create_at',
        'student_id',
    ];

    protected static function newFactory(): ScheduleSendWorkflowFormFactory
    {
        return ScheduleSendWorkflowFormFactory::new();
    }
}
