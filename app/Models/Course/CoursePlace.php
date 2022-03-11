<?php

namespace App\Models\Course;

use Database\Factories\Course\CoursePlaceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoursePlace extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['name'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    protected static function newFactory(): CoursePlaceFactory
    {
        return CoursePlaceFactory::new();
    }
}
