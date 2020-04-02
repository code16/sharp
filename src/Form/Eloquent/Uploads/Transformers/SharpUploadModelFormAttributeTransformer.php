<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Transformers;

use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;

class SharpUploadModelFormAttributeTransformer implements SharpAttributeTransformer
{
    /** @var bool */
    protected bool $withThumbnails;
    
    /** @var int */
    protected int $thumbnailWidth;
    
    /** @var int */
    protected int $thumbnailHeight;

    /**
     * @param bool $withThumbnails
     * @param int $thumbnailWidth
     * @param int $thumbnailHeight
     */
    public function __construct($withThumbnails = true, $thumbnailWidth = 1000, $thumbnailHeight = 400)
    {
        $this->withThumbnails = $withThumbnails;
        $this->thumbnailWidth = $thumbnailWidth;
        $this->thumbnailHeight = $thumbnailHeight;
    }

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
            return $instance->$attribute
                ->map(function($upload) {
                    $array = $this->transformUpload($upload);

                    $file = Arr::only($array, ["name", "thumbnail", "size"]);

                    return array_merge(
                        ["file" => sizeof($file) ? $file : null],
                        Arr::except($array, ["name", "thumbnail", "size"])
                    );
                })
                ->all();
        };

        return $this->transformUpload($instance->$attribute);
    }

    /**
     * @param $upload
     * @return array
     */
    protected function transformUpload($upload)
    {
        return array_merge(
            $upload->file_name
                ? [
                    "name" => $upload->file_name,
                    "thumbnail" => $this->withThumbnails 
                        ? $upload->thumbnail($this->thumbnailWidth, $this->thumbnailHeight)
                        : null,
                    "size" => $upload->size,
                ]
                : [],
            $upload->custom_properties ?? [],
            ["id" => $upload->id]
        );
    }
}