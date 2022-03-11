<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'parent_id',
        'unique_name',
        'name',
        'controller',
        'sort',
        'is_enabled',
    ];
}
