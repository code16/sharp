<?php

namespace Code16\Sharp\Show\Fields;

class SharpShowListField extends SharpShowField
{
    const FIELD_TYPE = "list";

    /** @var string */
    protected $label = null;

    /** @var array */
    protected $itemFields = [];

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key): SharpShowListField
    {
        return new static($key, static::FIELD_TYPE);
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function addItemField(SharpShowField $field): self
    {
        $this->itemFields[] = $field;

        return $this;
    }

    /**
     * @param string $key
     * @return SharpShowField
     */
    public function findItemFormFieldByKey(string $key)
    {
        return collect($this->itemFields)
            ->where("key", $key)
            ->first();
    }

    /**
     * @return array
     */
    protected function validationRules()
    {
        return [
            "itemFields" => "required|array",
        ];
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "label" => $this->label,
            "itemFields" => collect($this->itemFields)
                ->map(function(SharpShowField $field) {
                    return $field->toArray();
                })
                ->keyBy("key")
                ->all()
        ]);
    }
}