<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\TagsFormatter;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithDataLocalization;

class SharpFormTagsField extends SharpFormField
{
    use SharpFormFieldWithDataLocalization;

    const FIELD_TYPE = 'tags';

    protected bool $creatable = false;
    protected string $createText = 'Create';
    protected ?string $createAttribute = null;
    protected string $idAttribute = 'id';
    protected ?int $maxTagCount = null;
    protected array $options = [];
    protected array $createAdditionalAttributes = [];

    public static function make(string $key, array $options): self
    {
        $instance = new static($key, static::FIELD_TYPE, new TagsFormatter);

        $instance->options = collect($options)
            ->map(function ($label, $id) {
                return [
                    'id' => $id, 'label' => $label,
                ];
            })
            ->values()
            ->toArray();

        return $instance;
    }

    public function setCreatable(bool $creatable = true): self
    {
        $this->creatable = $creatable;

        return $this;
    }

    public function setCreateText(string $createText): self
    {
        $this->createText = $createText;

        return $this;
    }

    public function setCreateAttribute(string $attribute): self
    {
        $this->createAttribute = $attribute;

        return $this;
    }

    public function setCreateAdditionalAttributes(array $attributes): self
    {
        $this->createAdditionalAttributes = $attributes;

        return $this;
    }

    public function setIdAttribute($idAttribute): self
    {
        $this->idAttribute = $idAttribute;

        return $this;
    }

    public function setMaxTagCount(int $maxTagCount): self
    {
        $this->maxTagCount = $maxTagCount;

        return $this;
    }

    public function setMaxTagCountUnlimited(): self
    {
        $this->maxTagCount = null;

        return $this;
    }

    public function creatable(): bool
    {
        return $this->creatable;
    }

    public function createAttribute(): ?string
    {
        return $this->createAttribute;
    }

    public function createAdditionalAttributes(): array
    {
        return $this->createAdditionalAttributes;
    }

    public function idAttribute(): string
    {
        return $this->idAttribute;
    }

    public function options(): array
    {
        return $this->options;
    }

    protected function validationRules(): array
    {
        return [
            'options' => 'array',
            'creatable' => 'boolean',
            'maxTagCount' => 'integer',
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'creatable' => $this->creatable,
            'createText' => $this->createText,
            'maxTagCount' => $this->maxTagCount,
            'options' => $this->options,
            'localized' => $this->localized,
        ]);
    }
}
