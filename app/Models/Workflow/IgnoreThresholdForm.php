<?php

namespace App\Models\Workflow;

use Database\Factories\Workflow\IgnoreThresholdFormFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IgnoreThresholdForm extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const UPDATED_AT = null;

    protected $table = 'ignore_threshold_form';

    protected $fillable =
    [
        'origin_threshold_id',
        'user_id',
    ];

    protected static function newFactory(): IgnoreThresholdFormFactory
    {
        return IgnoreThresholdFormFactory::new();
    }
}
