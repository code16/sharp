<?php

namespace Code16\Sharp\Form\Eloquent\Uploads;

use Illuminate\Database\Eloquent\Model;

class SharpUploadModel extends Model
{
    protected $guarded = [];

    /**
     * @var array
     */
    protected $casts = [
        'custom_properties' => 'array',
    ];

    /**
     * @var array
     */
    protected $custom_properties;


    public function model()
    {
        return $this->morphTo();
    }

//    /**
//     * Return the full path of a file.
//     *
//     * @return mixed
//     */
//    function getSharpFilePath()
//    {
//        if ($this->model_type) {
//            $type = substr($this->model_type, strrpos($this->model_type, '\\') + 1);
//
//            return "$type/{$this->model_id}/{$this->file_name}";
//        }
//
//        return null;
//    }

//    /**
//     * @return $this
//     */
//    function getFileAttribute()
//    {
//        // The attribute "file" represents the file itself. In this
//        // case, it's the whole Model object, which plays 2 roles:
//        // list item and file item.
//        return $this;
//    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getAttribute($key)
    {
        if(!$this->isRealAttribute($key)) {
            return $this->custom_properties[$key] ?? null;
        }

        return parent::getAttribute($key);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return Model
     */
    public function setAttribute($key, $value)
    {
        if(!$this->isRealAttribute($key)) {
            return $this->updateCustomProperty($key, $value);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    private function updateCustomProperty($key, $value)
    {
        $properties = $this->custom_properties;
        $properties[$key] = $value;
        $this->setAttribute("custom_properties", $properties);

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    private function isRealAttribute(string $name)
    {
        return in_array($name, [
            "id", "model_id", "model_type", "model_key", "file_name",
            "mime_type", "disk", "size", "custom_properties",
            "order", "created_at", "updated_at", "file"
        ]);
    }

//    /**
//     * @param int $width
//     * @param int|null $height
//     * @param array $filters
//     * @return string
//     */
//    public function thumbnail($width, $height=null, $filters = [])
//    {
//        return (new Thumbnail($this))->thumbnail($width, $height, $filters);
//    }
//
//    /**
//     * @param int $width
//     * @param int $height
//     * @param array $filters
//     * @return string
//     */
//    public function fitThumbnail($width, $height, $filters = [])
//    {
//        return $this->thumbnail($width, $height, [
//                "fit" => ["w"=>$width, "h"=>$height]
//            ] + $filters);
//    }
//
//    /**
//     * @param int $width
//     * @param int $height
//     * @return string
//     */
//    public function fitAndGreyscaleThumbnail($width, $height)
//    {
//        return $this->fitThumbnail($width, $height, ["greyscale" => []]);
//    }
}