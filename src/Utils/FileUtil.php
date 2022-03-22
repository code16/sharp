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
        if (($pos = strrpos($fileName, '.')) !== false) {
            $ext = substr($fileName, $pos);
            $fileName = substr($fileName, 0, $pos);
        }

        return [$fileName, $ext ?? ''];
    }

    private function normalizeName(string $fileName)
    {
        return preg_replace('#[^A-Za-z0-9-_\\.]#', '', $fileName);
    }
}
