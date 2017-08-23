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
//    function setFileAttribute(array $value = null)
//    {
//        if(is_null($value) && $this->exists) {
//            $value = [
//                'path' => null, 'size' => null,
//                'mime' => null, 'disk' => null
//            ];
//        }
//
//        if(empty($value)) {
//            return;
//        }
//
//        if(array_key_exists("path", $value)) {
//            $this->setAttribute('file_name', $value["path"])
//                ->setAttribute('size', $value["size"])
//                ->setAttribute('mime_type', $value["mime"])
//                ->setAttribute('disk', $value["disk"]);
//        }
//
//        if($value["transformed"] ?? false && $this->exists) {
//            (new Thumbnail($this))->destroyAllThumbnails();
//        }
//    }

    /**
     * @param $value
     */
    public function setTransformedAttribute($value)
    {
        // The transformed attribute to true means there
        // was a transformation, we have to delete old thumbnails
        if($value && $this->exists) {
            (new Thumbnail($this))->destroyAllThumbnails();
        }
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
            "order", "created_at", "updated_at", "file", "transformed"
        ]);
    }

    /**
     * @param int|null $width
     * @param int|null $height
     * @param array $filters
     * @return string
     */
    public function thumbnail($width=null, $height=null, $filters=[])
    {
        return (new Thumbnail($this))->make($width, $height, $filters);
    }
}