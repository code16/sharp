<?php

namespace Code16\Sharp\Form\Eloquent\Uploads;

use Code16\Sharp\Form\Eloquent\Uploads\Thumbnails\Thumbnail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SharpUploadModel extends Model
{
    use FillsWithFileAttribute;

    protected $guarded = [];
    protected $casts = [
        'custom_properties' => 'array',
        'size' => 'integer',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }

    /**
     * @param  string  $key
     * @return mixed|null
     */
    public function getAttribute($key)
    {
        if ($key === 'file_name') {
            // when making the model in embed template (new Media($visual)), there may be a "path" defined instead of "file_name" (< 8.0 content)
            return parent::getAttribute('file_name') ?? $this->getAttribute('custom_properties')['path'] ?? null;
        }

        if (! $this->isRealAttribute($key)) {
            return $this->getAttribute('custom_properties')[$key] ?? null;
        }

        return parent::getAttribute($key);
    }

    /**
     * @param  string  $key
     * @param  mixed  $value
     * @return Model
     */
    public function setAttribute($key, $value)
    {
        if (! $this->isRealAttribute($key)) {
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

    public function thumbnail(?int $width = null, ?int $height = null, array $modifiers = []): string|Thumbnail|null
    {
        if (empty(func_get_args())) {
            return new Thumbnail($this);
        }

        return (new Thumbnail($this))
            ->when($modifiers, function (Thumbnail $thumb, array $modifiers) {
                foreach ($modifiers as $modifier) {
                    $thumb->addModifier($modifier);
                }
            })
            ->setAppendTimestamp()
            ->make($width, $height);
    }
}
