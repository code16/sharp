<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\Form\Fields\SharpFormField;
use Illuminate\Support\Facades\Storage;

class MarkdownFormatter implements SharpFieldFormatter
{

    /**
     * @param SharpFormField $field
     * @param $value
     * @return mixed
     */
    function toFront(SharpFormField $field, $value)
    {
        $array = [
            "text" => $value
        ];

        foreach($this->extractEmbeddedUploads($value) as $filename) {
            $upload = $this->getUpload($field, $filename);

            if($upload) {
                $array["files"][] = $upload;
            }
        }

        return $array;
    }

    /**
     * @param SharpFormField $field
     * @param string $attribute
     * @param $value
     * @return mixed
     */
    function fromFront(SharpFormField $field, string $attribute, $value)
    {
        $text = $value['text'] ?? '';

        if(isset($value["files"])) {
            $uploadFormatter = app(UploadFormatter::class);

            foreach($value["files"] as $file) {
                $upload = $uploadFormatter->fromFront($field, $attribute, $file);

                if(isset($upload["file_name"])) {
                    // New file was uploaded. We have to update
                    // the name of the file in the markdown
                    $text = str_replace(
                        "![]({$file["name"]})",
                        "![]({$upload["file_name"]})",
                        $text
                    );
                }
            }
        }

        return $text;
    }

    /**
     * @param string $md
     * @return array
     */
    protected function extractEmbeddedUploads($md)
    {
        preg_match_all(
            '/!\[[^\]]*\]\((?<filename>.*?)(?=\"|\))(?<optionalpart>\".*\")?\)/',
            $md, $matches, PREG_SET_ORDER, 0
        );

        return collect($matches)->map(function($match) {
            return trim($match["filename"]);
        })->all();
    }

    /**
     * @param $field
     * @param $filename
     * @return array
     */
    protected function getUpload($field, $filename)
    {
        $model = new SharpUploadModel([
            "file_name" => $filename,
            "disk" => $field->storageDisk(),
            "size" => $this->getFileSize($field, $filename)
        ]);

        return [
            "name" => $model->file_name,
            "size" => $model->size,
            "thumbnail" => $model->thumbnail(null, 150)
        ];
    }

    /**
     * @param $field
     * @param $filename
     * @return mixed
     */
    protected function getFileSize($field, $filename)
    {
        try {
            return Storage::disk($field->storageDisk())->size($filename);

        } catch(\RuntimeException $ex) {
            return null;
        }
    }

}