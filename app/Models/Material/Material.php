<?php

namespace App\Models\Material;

use App\Models\Auth\User;
use App\Models\Unit;
use Database\Factories\Material\MaterialFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const UPDATED_AT = null;

    public const CREATED_AT = null;

    protected $fillable = [
        'folder_id',
        'type',
        'source',
        'owner',
    ];

    public function authUser(): MorphToMany
    {
        return $this->morphedByMany(
            User::class,
            'authorize',
            'material_authorizes',
            'material_id'
        )->withPivot('id');
    }

    public function authUnit(): MorphToMany
    {
        return $this->morphedByMany(
            Unit::class,
            'authorize',
            'material_authorizes',
            'material_id'
        )->withPivot('id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'folder_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'folder_id');
    }

    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    public function belong(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner');
    }

    protected static function newFactory(): MaterialFactory
    {
        return MaterialFactory::new();
    }
}
