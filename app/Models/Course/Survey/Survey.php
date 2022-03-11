<?php

namespace App\Models\Course\Survey;

use App\Models\Auth\User;
use Database\Factories\Course\Survey\SurveyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Survey extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = ['metadata' => 'array'];

    protected $fillable = [
        'created_by',
        'updated_by',
        'name',
        'public',
        'version',
        'origin',
        'unit_id',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'origin');
    }

    public function childrens(): HasMany
    {
        return $this->hasMany(self::class, 'origin');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class);
    }

    protected static function newFactory(): SurveyFactory
    {
        return SurveyFactory::new();
    }
}
