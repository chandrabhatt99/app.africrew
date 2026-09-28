<?php

if (!function_exists('get_storage_url')) {
    function get_storage_url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        $cleanPath = ltrim($path, '/');
        if (str_starts_with($cleanPath, 'storage/')) {
            $cleanPath = substr($cleanPath, 8);
        }
        if (str_starts_with($cleanPath, 'public/storage/')) {
            $cleanPath = substr($cleanPath, 15);
        }

        // Live cPanel environment check
        if (isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], 'databrainit.com')) {
            return 'https://africrew-crm.databrainit.com/public/storage/' . $cleanPath;
        }

        return asset('storage/' . $cleanPath);
    }
}
