<?php

namespace Tests\Unit\Services;

use App\Services\ImageOptimizer;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests that uploaded images get resized and re-encoded to WebP (US-35).
 */
class ImageOptimizerTest extends TestCase
{
    private function fakeJpeg(int $width, int $height): string
    {
        $image = imagecreatetruecolor($width, $height);
        imagefill($image, 0, 0, imagecolorallocate($image, 200, 50, 50));

        ob_start();
        imagejpeg($image);
        $contents = ob_get_clean();
        imagedestroy($image);

        return $contents;
    }

    #[Test]
    public function it_converts_the_image_to_webp(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('cars/photo.jpg', $this->fakeJpeg(400, 300));

        $newPath = (new ImageOptimizer())->optimize('public', 'cars/photo.jpg', maxWidth: 1200);

        $this->assertSame('cars/photo.webp', $newPath);
        Storage::disk('public')->assertExists('cars/photo.webp');
        Storage::disk('public')->assertMissing('cars/photo.jpg');
    }

    #[Test]
    public function it_scales_down_images_wider_than_the_max_width(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('cars/wide.jpg', $this->fakeJpeg(2000, 1000));

        $newPath = (new ImageOptimizer())->optimize('public', 'cars/wide.jpg', maxWidth: 1200);

        $contents = Storage::disk('public')->get($newPath);
        $size = getimagesizefromstring($contents);

        $this->assertSame(1200, $size[0]);
        $this->assertSame(600, $size[1]);
    }

    #[Test]
    public function it_does_not_upscale_images_smaller_than_the_max_width(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('cars/small.jpg', $this->fakeJpeg(400, 300));

        $newPath = (new ImageOptimizer())->optimize('public', 'cars/small.jpg', maxWidth: 1200);

        $contents = Storage::disk('public')->get($newPath);
        $size = getimagesizefromstring($contents);

        $this->assertSame(400, $size[0]);
        $this->assertSame(300, $size[1]);
    }
}
