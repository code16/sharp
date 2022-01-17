<?php

namespace Code16\Sharp\Form\Eloquent\Uploads;

use Code16\Sharp\Form\Eloquent\Uploads\Thumbnails\Thumbnail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SharpUploadModel extends Model
{
    protected $guarded = [];
    protected $casts = [
        'custom_properties' => 'array',
        'size'              => 'integer',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }

    public function setFileAttribute($value)
    {
        // We use this magical "file" attribute to fill at the same time
        // file_name, mime_type, disk and size in a MorphMany case
        if ($value) {
            $this->fill($value);
        }
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function getAttribute($key)
    {
        if (!$this->isRealAttribute($key)) {
            return $this->getAttribute('custom_properties')[$key] ?? null;
        }

        return parent::getAttribute($key);
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return Model
     */
    public function setAttribute($key, $value)
    {
        if (!$this->isRealAttribute($key)) {
            return $this->updateCustomProperty($key, $value);
        }

        return parent::setAttribute($key, $value);
    }

    protected function updateCustomProperty(string $key, $value): self
    {
        $properties = $this->getAttribute('custom_properties');
        $properties[$key] = $value;
        $this->setAttribute('custom_properties', $properties);

        return $this;
    }

    protected function isRealAttribute(string $name): bool
    {
        return in_array($name, [
            'id', 'model', 'model_id', 'model_type', 'model_key', 'file_name',
            'mime_type', 'disk', 'size', 'custom_properties',
            'order', 'created_at', 'updated_at', 'file', 'transformed',
        ]);
    }

    public function thumbnail(int $width = null, int $height = null, array $customFilters = []): ?string
    {
        return (new Thumbnail($this))
            ->setTransformationFilters($this->filters ?: null)
            ->setAppendTimestamp()
            ->make($width, $height, $customFilters);
    }
}
