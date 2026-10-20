<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Throwable;

/**
 * Shrinks uploaded product photos (max 1200px wide) and converts them to WebP
 * when the PHP GD extension supports it. If anything goes wrong the original
 * file is saved untouched, so an upload never fails because of optimizing.
 */
class ImageOptimizer
{
    public const MAX_WIDTH = 1200;
    public const QUALITY   = 82;

    /**
     * Save the upload into $dir and return the final file name.
     */
    public function save(UploadedFile $file, string $dir, string $fileName): string
    {
        try {
            if (! extension_loaded('gd') || ! function_exists('imagewebp')) {
                return $this->plain($file, $dir, $fileName);
            }

            $mime = (string) $file->getMimeType();
            $path = $file->getRealPath();

            $img = match ($mime) {
                'image/jpeg' => @imagecreatefromjpeg($path),
                'image/png'  => @imagecreatefrompng($path),
                'image/webp' => @imagecreatefromwebp($path),
                default      => false,
            };

            if (! $img) {
                return $this->plain($file, $dir, $fileName);
            }

            $w = imagesx($img);
            $h = imagesy($img);

            if ($w > self::MAX_WIDTH) {
                $newH   = (int) round($h * (self::MAX_WIDTH / $w));
                $scaled = imagescale($img, self::MAX_WIDTH, $newH);

                if ($scaled) {
                    imagedestroy($img);
                    $img = $scaled;
                }
            }

            imagepalettetotruecolor($img);
            imagealphablending($img, true);
            imagesavealpha($img, true);

            $webpName = pathinfo($fileName, PATHINFO_FILENAME) . '.webp';

            $ok = imagewebp($img, rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . $webpName, self::QUALITY);
            imagedestroy($img);

            return $ok ? $webpName : $this->plain($file, $dir, $fileName);
        } catch (Throwable $e) {
            report($e);

            return $this->plain($file, $dir, $fileName);
        }
    }

    private function plain(UploadedFile $file, string $dir, string $fileName): string
    {
        $file->move($dir, $fileName);

        return $fileName;
    }
}
