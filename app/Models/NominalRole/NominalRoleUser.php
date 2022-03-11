<?php

namespace App\Models\NominalRole;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NominalRoleUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nominal_role_id',
        'roleable_type',
        'roleable_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(NominalRole::class, 'nominal_role_id');
    }

    public function roleable(): MorphTo
    {
        return $this->morphTo();
    }
}
