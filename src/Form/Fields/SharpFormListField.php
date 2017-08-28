<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\ListFormatter;

class SharpFormListField extends SharpFormField
{
    const FIELD_TYPE = "list";

    /**
     * @var bool
     */
    protected $addable = false;

    /**
     * @var bool
     */
    protected $sortable = false;

    /**
     * @var bool
     */
    protected $removable = false;

    /**
     * @var string
     */
    protected $addText = "Add an item";

    /**
     * @var string
     */
    protected $itemIdAttribute = "id";

    /**
     * @var string
     */
    protected $orderAttribute = null;

    /**
     * @var int
     */
    protected $maxItemCount = null;

    /**
     * @var string
     */
    protected $collapsedItemTemplate = null;

    /**
     * @var array
     */
    protected $itemFields = [];

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        return new static($key, static::FIELD_TYPE, new ListFormatter);
    }

    /**
     * @param bool $addable
     * @return static
     */
    public function setAddable(bool $addable = true)
    {
        $this->addable = $addable;

        return $this;
    }

    /**
     * @param bool $sortable
     * @return static
     */
    public function setSortable(bool $sortable = true)
    {
        $this->sortable = $sortable;

        return $this;
    }

    /**
     * @param bool $removable
     * @return static
     */
    public function setRemovable(bool $removable = true)
    {
        $this->removable = $removable;

        return $this;
    }

    /**
     * @param string $addText
     * @return static
     */
    public function setAddText(string $addText)
    {
        $this->addText = $addText;

        return $this;
    }

    /**
     * @param string $orderAttribute
     * @return static
     */
    public function setOrderAttribute(string $orderAttribute)
    {
        $this->orderAttribute = $orderAttribute;

        return $this;
    }

    /**
     * @param int $maxItemCount
     * @return static
     */
    public function setMaxItemCount(int $maxItemCount)
    {
        $this->maxItemCount = $maxItemCount;

        return $this;
    }

    /**
     * @param string $collapsedItemTemplate
     * @return static
     */
    public function setCollapsedItemTemplate(string $collapsedItemTemplate)
    {
        $this->collapsedItemTemplate = $collapsedItemTemplate;

        return $this;
    }

    /**
     * @param SharpFormField $field
     * @return static
     */
    public function addItemField(SharpFormField $field)
    {
        $this->itemFields[] = $field;

        return $this;
    }

    /**
     * @param string $key
     * @return SharpFormField
     */
    public function findItemFormFieldByKey(string $key)
    {
        return collect($this->itemFields)->where("key", $key)->first();
    }

    /**
     * @param string $itemIdAttribute
     * @return static
     */
    public function setItemIdAttribute(string $itemIdAttribute)
    {
        $this->itemIdAttribute = $itemIdAttribute;

        return $this;
    }

    /**
     * @return string
     */
    public function orderAttribute()
    {
        return $this->orderAttribute;
    }

    /**
     * @return string
     */
    public function itemIdAttribute()
    {
        return $this->itemIdAttribute;
    }

    /**
     * @return bool
     */
    public function isSortable(): bool
    {
        return $this->sortable;
    }


    /**
     * @return array
     */
    protected function validationRules()
    {
        return [
            "itemFields" => "required|array",
            "itemIdAttribute" => "required",
            "addable" => "boolean",
            "removable" => "boolean",
            "sortable" => "boolean",
            "maxItemCount" => "integer",
        ];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "addable" => $this->addable,
            "removable" => $this->removable,
            "sortable" => $this->sortable,
            "addText" => $this->addText,
            "collapsedItemTemplate" => $this->collapsedItemTemplate,
            "maxItemCount" => $this->maxItemCount,
            "itemIdAttribute" => $this->itemIdAttribute,
            "itemFields" => collect($this->itemFields)->map(function($field) {
                return $field->toArray();
            })->keyBy("key")->all()
        ]);
    }
}