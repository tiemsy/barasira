<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileAvatarService
{
    public function store(User $user, UploadedFile $avatar): string
    {
        $path = $avatar->storePublicly("avatars/{$user->id}", 'public');

        return Storage::disk('public')->url($path);
    }

    public function deleteLocal(?string $avatarUrl): void
    {
        $path = parse_url($avatarUrl ?? '', PHP_URL_PATH);
        $storagePath = is_string($path) ? ltrim($path, '/') : '';

        if (! str_starts_with($storagePath, 'storage/avatars/')) {
            return;
        }

        Storage::disk('public')->delete(str_replace('storage/', '', $storagePath));
    }
}
