<?php

namespace Code16\Sharp\Form\Eloquent\Uploads;

use Code16\Sharp\Form\Eloquent\Uploads\Thumbnails\Thumbnail;
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

    public function model()
    {
        return $this->morphTo('model');
    }

    /**
     * @param array $value
     */
    function setFileAttribute(array $value = null)
    {
        if(is_null($value) && $this->exists) {
            $value = [
                'path' => null, 'size' => null,
                'mime' => null, 'disk' => null
            ];
        }

        if(empty($value)) {
            return;
        }

        if(array_key_exists("path", $value)) {
            $this->setAttribute('file_name', $value["path"]);
            $this->setAttribute('size', $value["size"]);
            $this->setAttribute('mime_type', $value["mime"]);
            $this->setAttribute('disk', $value["disk"]);
        }

        if($value["transformed"] ?? false && $this->exists) {
            (new Thumbnail($this))->destroyAllThumbnails();
        }
    }

    /**
     * @return array|null
     */
    function getFileAttribute()
    {
        $filename = $this->getAttribute("file_name");
        return $filename ? [
            "name" => $this->getAttribute("file_name"),
            "thumbnail" => $this->thumbnail(null, 150),
            "size" => $this->getAttribute("size"),
        ] : null;
    }

    /**
     * @return array
     */
    function toArray()
    {
        return [
            "id" => $this->getAttribute("id"),
            "file" => $this->getFileAttribute()
        ] + $this->getAttribute("custom_properties") ?? [];
    }


    /**
     * @param string $key
     * @return mixed|null
     */
    public function getAttribute($key)
    {
        if(!$this->isRealAttribute($key)) {
            return $this->getAttribute("custom_properties")[$key] ?? null;
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
        $properties = $this->getAttribute("custom_properties");
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

    /**
     * @param int $width
     * @param int|null $height
     * @param array $filters
     * @return string
     */
    public function thumbnail($width, $height=null, $filters = [])
    {
        return (new Thumbnail($this))->make($width, $height, $filters);
    }
}