<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

final class PublicImage
{
    public static function url(?string $storedPath): ?string
    {
        $path = trim((string) $storedPath);
        if ($path === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $path) === 1) {
            return $path;
        }

        $path = str_replace('\\', '/', $path);
        if (preg_match('#(?:^|/)storage/app/public/(.+)$#i', $path, $matches) === 1) {
            $path = $matches[1];
        } else {
            $path = preg_replace('#^(?:\./)?(?:public/|storage/)#i', '', ltrim($path, '/')) ?? '';
        }

        if ($path === '' || preg_match('#(^|/)\.\.?(/|$)#', $path) === 1) {
            return null;
        }

        return Storage::disk('public')->exists($path)
            ? asset('storage/'.$path)
            : null;
    }
}
