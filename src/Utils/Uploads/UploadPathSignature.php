<?php

namespace Code16\Sharp\Utils\Uploads;

class UploadPathSignature
{
    public static function enabled(): bool
    {
        return (bool) sharp()->config()->get('uploads.sign_paths');
    }

    public static function make(?string $disk, string $path): string
    {
        return static::hash($disk, $path, app('encrypter')->getKey());
    }

    public static function check(mixed $signature, mixed $disk, mixed $path): bool
    {
        if (! is_string($signature) || ! is_string($path) || (! is_string($disk) && $disk !== null)) {
            return false;
        }

        // Handles APP_PREVIOUS_KEYS, like Laravel signed URLs do
        foreach (app('encrypter')->getAllKeys() as $key) {
            if (hash_equals(static::hash($disk, $path, $key), $signature)) {
                return true;
            }
        }

        return false;
    }

    private static function hash(?string $disk, string $path, string $key): string
    {
        return hash_hmac('sha256', "sharp-upload-path\0".$disk."\0".$path, $key);
    }
}
