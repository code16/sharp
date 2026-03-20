<?php

namespace Code16\Sharp\Utils;

use Illuminate\Support\Facades\Storage;

class FileUtil
{
    public function findAvailableName(string $fileName, string $path = '', string $disk = 'local'): string
    {
        $k = 1;

        [$baseFileName, $ext] = $this->explodeExtension($fileName);
        $baseFileName = $this->normalizeName($baseFileName);
        $fileName = $baseFileName.$ext;

        while (Storage::disk($disk)->exists("$path/$fileName")) {
            $fileName = $baseFileName.'-'.($k++).$ext;
        }

        return $fileName;
    }

    public function explodeExtension(string $fileName): array
    {
        $pathinfo = pathinfo($fileName);

        return [
            $pathinfo['filename'] ?? '',
            isset($pathinfo['extension']) ? '.'.$pathinfo['extension'] : '',
        ];
    }

    private function normalizeName(string $fileName): array|string|null
    {
        return preg_replace('#[^A-Za-z0-9-_.]#', '', $fileName);
    }
}
