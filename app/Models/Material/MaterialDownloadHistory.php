<?php

namespace App\Models\Material;

use Database\Factories\Material\MaterialDownloadHistoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialDownloadHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_material_id',
        'student',
        'opened_counts',
        'downloaded_counts',
        'reading_time',
    ];

    public function belong(): BelongsTo
    {
        return $this->belongsTo(CourseMaterial::class, 'course_material_id');
    }

    protected static function newFactory(): MaterialDownloadHistoryFactory
    {
        return MaterialDownloadHistoryFactory::new();
    }
}
