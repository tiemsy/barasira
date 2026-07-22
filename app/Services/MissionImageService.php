<?php

namespace App\Services;

use App\Models\Mission;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MissionImageService
{
    /** @param array<int, UploadedFile> $images */
    public function replace(Mission $mission, array $images): void
    {
        $disk = Storage::disk('public');
        $newPaths = [];

        try {
            foreach ($images as $image) {
                $newPaths[] = $image->store("missions/{$mission->id}", 'public');
            }

            $oldPaths = DB::transaction(function () use ($mission, $newPaths): array {
                $oldPaths = $mission->images()->pluck('path')->all();
                $mission->images()->delete();

                foreach ($newPaths as $position => $path) {
                    $mission->images()->create(['path' => $path, 'sort_order' => $position]);
                }

                return $oldPaths;
            });

            $disk->delete($oldPaths);
        } catch (Throwable $exception) {
            $disk->delete($newPaths);
            throw $exception;
        }
    }
}
