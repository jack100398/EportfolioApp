<?php

namespace App\Models;

use Database\Factories\FileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'extension',
        'size',
        'directory',
        'created_by',
        'remarks',
    ];

    protected static function newFactory(): FileFactory
    {
        return FileFactory::new();
    }
}
