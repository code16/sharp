<?php

namespace Code16\Sharp\Utils;

use Illuminate\Support\Facades\Storage;

class FileUtil
{

    /**
     * @param string $fileName
     * @param string $path
     * @param string $disk
     * @return string
     */
    public function findAvailableName(string $fileName, string $path = "", string $disk = "local")
    {
        $k = 1;

        list($baseFileName, $ext) = $this->explodeExtension($fileName);

        $baseFileName = $this->normalizeName($baseFileName);

        while (Storage::disk($disk)->exists("$path/$fileName")) {
            $fileName = $baseFileName . "-" . ($k++) . $ext;
        }

        return $fileName;
    }

    /**
     * @param string $fileName
     * @return array
     */
    public function explodeExtension(string $fileName)
    {
        $ext = "";

        if (($pos = strrpos($fileName, '.')) !== false) {
            $ext = substr($fileName, $pos);
            $fileName = substr($fileName, 0, $pos);
        }

        return [$fileName, $ext];
    }

    /**
     * @param string $fileName
     * @return mixed
     */
    private function normalizeName(string $fileName)
    {
        return preg_replace("#[^A-Za-z1-9-_\\.]#", "", $fileName);
    }
}