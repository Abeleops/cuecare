<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ImageUploadService
{
    public function store(UploadedFile $file, string $directory, string $field): string
    {
        if (! function_exists('imagecreatefromstring')) {
            throw ValidationException::withMessages([$field => 'Image processing is unavailable.']);
        }

        $contents = file_get_contents($file->getRealPath());
        $image = $contents === false ? false : @imagecreatefromstring($contents);
        $mime = $file->getMimeType();
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        $webpIsUnavailable = $mime === 'image/webp' && ! function_exists('imagewebp');

        if (! $image || ! isset($extensions[$mime]) || $webpIsUnavailable) {
            throw ValidationException::withMessages([$field => 'The image could not be processed.']);
        }

        // Re-encode the pixels so uploaded metadata and trailing content are not stored
        imagealphablending($image, false);
        imagesavealpha($image, true);
        ob_start();
        try {
            $encoded = match ($mime) {
                'image/jpeg' => imagejpeg($image, null, 90),
                'image/png' => imagepng($image),
                'image/webp' => imagewebp($image, null, 90),
            };
            $bytes = ob_get_contents();
        } finally {
            ob_end_clean();
            imagedestroy($image);
        }

        if (! $encoded || $bytes === false) {
            throw ValidationException::withMessages([$field => 'The image could not be processed.']);
        }

        $path = $directory.'/'.Str::random(40).'.'.$extensions[$mime];
        if (! Storage::disk('public')->put($path, $bytes)) {
            throw ValidationException::withMessages([$field => 'The image could not be saved.']);
        }

        return $path;
    }
}
