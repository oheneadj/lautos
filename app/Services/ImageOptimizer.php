<?php

/**
 * Resizes and re-encodes images to WebP after upload, so car photos don't
 * ship at whatever resolution/format a customer's phone produced.
 *
 * @author Ohene Adjei
 */

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;

class ImageOptimizer
{
    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = ImageManager::usingDriver(Driver::class);
    }

    /**
     * Re-encodes an already-uploaded file on the given disk to WebP, scaled
     * down to $maxWidth if it's larger. Returns the new path (the original
     * is deleted if the extension changed) — callers should persist this
     * return value instead of the path they passed in.
     */
    public function optimize(string $disk, string $path, int $maxWidth = 1200): string
    {
        // A .webp path means this file already went through optimize() on a
        // previous save — EditCar resubmits every existing path on every
        // save, touched or not, so without this guard every untouched photo
        // gets needlessly re-decoded and re-compressed (lossy) each time.
        if (str_ends_with($path, '.webp')) {
            return $path;
        }

        $image = $this->manager->decode(Storage::disk($disk)->get($path));
        $image->scaleDown(width: $maxWidth);

        $newPath = preg_replace('/\.\w+$/', '.webp', $path);
        Storage::disk($disk)->put($newPath, (string) $image->encodeUsingFormat(Format::WEBP, quality: 80));

        if ($newPath !== $path) {
            Storage::disk($disk)->delete($path);
        }

        return $newPath;
    }
}
