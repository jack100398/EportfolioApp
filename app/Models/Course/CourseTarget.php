<?php

namespace App\Models\Course;

use Database\Factories\Course\CourseTargetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseTarget extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'target_name',
        'sort',
        'viewable',
    ];

    protected static function newFactory(): CourseTargetFactory
    {
        return CourseTargetFactory::new();
    }
}
