<?php

namespace Code16\Sharp\Utils;

use Illuminate\Support\Facades\Storage;

class UploadUtil
{

    /**
     * @param string $dirPath
     * @param string $fileName
     * @param string $disk
     * @return string
     */
    public function findAvailableName(string $dirPath, string $fileName, string $disk = "local")
    {
        $k = 1;
        $baseFileName = $this->normalizeName($fileName);

        list($fileName, $ext) = $this->explodeExtension($fileName);

        while (Storage::disk($disk)->exists("$dirPath/$fileName")) {
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
     * @return string
     */
    public function getTmpUploadDirectory()
    {
        $dir = storage_path(config("sharp.uploads.tmp_dir"));

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir;
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