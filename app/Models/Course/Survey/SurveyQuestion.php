<?php

namespace App\Models\Course\Survey;

use Database\Factories\Course\Survey\SurveyQuestionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    public const CREATED_AT = null;

    protected $casts = ['metadata' => 'array'];

    protected $fillable = [
        'survey_id',
        'sort',
        'content',
        'type',
        'metadata',
    ];

    protected static function newFactory(): SurveyQuestionFactory
    {
        return SurveyQuestionFactory::new();
    }
}
