<?php

namespace App\Models\Course;

use Database\Factories\Course\CreditFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Credit extends Model
{
    use HasFactory;

    protected $casts = ['training_time' => 'array'];

    public $timestamps = false;

    protected $fillable = [
        'year',
        'sort',
        'parent_id',
        'credit_name',
        'credit_type',
        'training_time',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    protected static function newFactory(): CreditFactory
    {
        return CreditFactory::new();
    }
}
