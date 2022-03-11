<?php

namespace App\Models\Course\Survey;

use Database\Factories\Course\Survey\CourseSurveyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseSurvey extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const UPDATED_AT = null;

    protected $fillable = [
        'survey_id',
        'created_at',
        'start_at',
        'end_at',
    ];

    public function survey(): BelongsTo
    {
        return $this->BelongsTo(Survey::class, 'survey_id');
    }

    public function records(): HasMany
    {
        return $this->hasMany(CourseSurveyRecord::class);
    }

    protected static function newFactory(): CourseSurveyFactory
    {
        return CourseSurveyFactory::new();
    }
}
