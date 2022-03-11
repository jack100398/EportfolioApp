<?php

namespace App\Models\Admin;

use Database\Factories\Auth\UserMetadataFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMetadata extends Model
{
    use HasFactory;

    /** 證書 */
    public const CERTIFICATES = 'certificates';

    /** 快速連結 */
    public const QUICK_LINKS = 'quick_links';

    /** 個人偏好設置 */
    public const PREFERENCES = 'preferences';

    /** 個人資料 */
    public const PROFILES = 'profiles';

    /** 代理人 */
    public const SUBSTITUTES = 'substitutes';

    protected $fillable = [
        'user_id',
        'key',
        'value',
    ];

    protected static function newFactory(): \Illuminate\Database\Eloquent\Factories\Factory
    {
        return UserMetadataFactory::new();
    }
}
