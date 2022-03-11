<?php

namespace App\Models\Workflow;

use App\Models\Auth\User;
use Database\Factories\Workflow\ProcessFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Process extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const UPDATED_AT = null;

    protected $table = 'process';

    protected $fillable =
    [
        'id',
        'workflow_id',
        'next_process_id',
        'type',
        'state',
        'error_status',
        'is_default',
        'sign_by',
        'role',
        'opinion',
    ];

    public function nextProcess(): HasOne
    {
        return $this->hasOne(self::class, 'next_process_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sign_by');
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    protected static function newFactory(): ProcessFactory
    {
        return ProcessFactory::new();
    }
}
