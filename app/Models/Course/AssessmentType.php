<?php

namespace App\Models\Course;

use Database\Factories\Course\AssessmentTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentType extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'type',
        'assessment_name',
        'unit_id',
        'source',
    ];

    protected static function newFactory(): AssessmentTypeFactory
    {
        return AssessmentTypeFactory::new();
    }
}
