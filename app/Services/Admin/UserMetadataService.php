<?php

namespace App\Services\Admin;

use App\Models\Admin\UserMetadata;

/**
 * TODO: Implement.
 * TODO: 老師個人評語.
 */
class UserMetadataService
{
    public function save(int $userId, string $key, string $value): void
    {
        $actions = [
            UserMetadata::CERTIFICATES => 'saveCertificates',
            UserMetadata::QUICK_LINKS => 'saveQuickLinks',
            UserMetadata::PREFERENCES => 'savePreferences',
            UserMetadata::PROFILES => 'saveProfiles',
            UserMetadata::SUBSTITUTES => 'saveSubstitutes',
        ];

        $action = $actions[$key];

        if (is_callable([$this, $action])) {
            $this->$action($userId, $value);
        } else {
            throw new \InvalidArgumentException('Unknown key');
        }
    }

    private function saveCertificates(int $userId, string $value): void
    {
    }

    private function saveQuickLinks(int $userId, string $value): void
    {
    }

    private function savePreferences(int $userId, string $value): void
    {
    }

    private function saveProfiles(int $userId, string $value): void
    {
    }

    private function saveSubstitutes(int $userId, string $value): void
    {
    }
}
