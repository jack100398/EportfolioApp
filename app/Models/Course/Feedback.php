<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const UPDATED_AT = null;

    public const CREATED_AT = null;

    protected $fillable = [
        'comment',
        'public',
        'usage',
        'created_by',
    ];
}
