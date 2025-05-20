<?php

namespace Code16\Sharp\Form\Eloquent\Uploads;

trait FillsWithFileAttribute
{
    public static function bootFillsWithFileAttribute(): void
    {
        static::saving(function (SharpUploadModel $uploadModel) {
            if (isset($uploadModel->file) && is_array($uploadModel->file)) {
                // We use this magical "file" attribute to fill at the same time
                // file_name, mime_type, disk and size in a MorphMany case
                $uploadModel->fill($uploadModel->file);
            }
            unset($uploadModel->file);
        });
    }
}
