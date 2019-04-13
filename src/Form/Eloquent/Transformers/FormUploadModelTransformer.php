<?php

namespace Code16\Sharp\Form\Eloquent\Transformers;

use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class FormUploadModelTransformer implements SharpAttributeTransformer
{

    /**
     * Transform a model attribute to array (json-able).
     *
     * @param $value
     * @param $instance
     * @param string $attribute
     * @return mixed
     */
    function apply($value, $instance = null, $attribute = null)
    {
        if(!$instance->$attribute) {
            return null;
        }

        if($instance->$attribute() instanceof MorphMany) {
            // We are handling a list of uploads
            return $instance->$attribute->map(function($upload) {
                $array = $this->transformUpload($upload);

                $file = array_only($array, ["name", "thumbnail", "size"]);

                return [
                    "file" => sizeof($file) ? $file : null,
                ] + array_except($array, ["name", "thumbnail", "size"]);
            })->all();
        };

        return $this->transformUpload($instance->$attribute);
    }

    /**
     * @param $upload
     * @return array
     */
    protected function transformUpload($upload)
    {
        return ($upload->file_name ? [
                "name" => $upload->file_name,
                "thumbnail" => $upload->thumbnail(config("sharp.uploads.default_thumbnail.width", 1000), config("sharp.uploads.default_thumbnail.height", 400)),
                "size" => $upload->size,
            ] : [])
            + ($upload->custom_properties ?? [])
            + ["id" => $upload->id];
    }
}
