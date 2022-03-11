<?php

namespace App\Models\Exam;

use App\Models\Auth\User;
use Database\Factories\Exam\ExamFolderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamFolder extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const TYPE_OFFICIAL = 1;

    public const TYPE_PERSONAL = 2;

    public const TYPES = [
        self::TYPE_OFFICIAL,
        self::TYPE_PERSONAL,
    ];

    protected $fillable = [
        'name',
        'parent_id',
        'type',
        'created_by',
    ];

    public function authUsers(): MorphToMany
    {
        return $this->morphedByMany(
            User::class,
            'authorizable',
            'exam_folder_authorizations',
            'folder_id'
        );
    }

    public function createdUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function examQuestions(): HasMany
    {
        return $this->hasMany(ExamQuestion::class, 'folder_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    protected static function newFactory(): ExamFolderFactory
    {
        return ExamFolderFactory::new();
    }
}
