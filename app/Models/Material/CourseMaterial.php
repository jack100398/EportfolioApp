<?php

namespace App\Models\Material;

use Database\Factories\Material\CourseMaterialFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'material_id',
        'description',
        'required_time',
        'opened_at',
        'ended_at',
        'created_by',
        'updated_by',
    ];

    protected static function newFactory(): CourseMaterialFactory
    {
        return CourseMaterialFactory::new();
    }
}
