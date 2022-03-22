<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\ListFormatter;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithTemplates;

class SharpFormListField extends SharpFormField
{
    use SharpFormFieldWithTemplates;

    const FIELD_TYPE = 'list';

    protected bool $addable = false;
    protected bool $sortable = false;
    protected bool $removable = false;
    protected string $addText = 'Add an item';
    protected string $itemIdAttribute = 'id';
    protected ?string $orderAttribute = null;
    protected ?int $maxItemCount = null;
    protected array $itemFields = [];
    protected bool $allowBulkUpload = false;
    private ?string $bulkUploadItemFieldKey = null;
    private int $bulkUploadFileCountLimit = 10;

    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE, new ListFormatter());
    }

    public function setAddable(bool $addable = true): self
    {
        $this->addable = $addable;

        return $this;
    }

    public function setSortable(bool $sortable = true): self
    {
        $this->sortable = $sortable;

        return $this;
    }

    public function setRemovable(bool $removable = true): self
    {
        $this->removable = $removable;

        return $this;
    }

    public function setAddText(string $addText): self
    {
        $this->addText = $addText;

        return $this;
    }

    public function setOrderAttribute(string $orderAttribute): self
    {
        $this->orderAttribute = $orderAttribute;

        return $this;
    }

    public function setMaxItemCount(int $maxItemCount = null): self
    {
        if ($maxItemCount === null) {
            return $this->setMaxItemCountUnlimited();
        }

        $this->maxItemCount = $maxItemCount;

        return $this;
    }

    public function setMaxItemCountUnlimited(): self
    {
        $this->maxItemCount = null;

        return $this;
    }

    public function allowBulkUploadForField(string $itemFieldKey): self
    {
        $this->allowBulkUpload = true;
        $this->bulkUploadItemFieldKey = $itemFieldKey;

        return $this;
    }

    public function doNotAllowBulkUpload(): self
    {
        $this->allowBulkUpload = false;

        return $this;
    }

    public function setBulkUploadFileCountLimitAtOnce(int $limit): self
    {
        $this->bulkUploadFileCountLimit = $limit;

        return $this;
    }

    public function setCollapsedItemInlineTemplate(string $collapsedItemInlineTemplate): self
    {
        $this->setInlineTemplate($collapsedItemInlineTemplate, 'item');

        return $this;
    }

    public function setCollapsedItemTemplatePath(string $collapsedItemTemplatePath): self
    {
        $this->setTemplatePath($collapsedItemTemplatePath, 'item');

        return $this;
    }

    public function addItemField(SharpFormField $field): self
    {
        $this->itemFields[] = $field;

        return $this;
    }

    public function setItemIdAttribute(string $itemIdAttribute): self
    {
        $this->itemIdAttribute = $itemIdAttribute;

        return $this;
    }

    public function itemFields(): \Illuminate\Support\Collection
    {
        return collect($this->itemFields);
    }

    public function findItemFormFieldByKey(string $key): ?SharpFormField
    {
        return $this->itemFields()->where('key', $key)->first();
    }

    public function orderAttribute(): ?string
    {
        return $this->orderAttribute;
    }

    public function itemIdAttribute(): string
    {
        return $this->itemIdAttribute;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    protected function validationRules(): array
    {
        return [
            'itemFields' => 'required|array',
            'itemIdAttribute' => 'required',
            'addable' => 'boolean',
            'removable' => 'boolean',
            'sortable' => 'boolean',
            'allowBulkUpload' => 'boolean',
            'bulkUploadItemFieldKey' => 'nullable|string',
            'bulkUploadFileCountLimit' => 'integer',
            'maxItemCount' => 'nullable|integer',
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'addable' => $this->addable,
            'removable' => $this->removable,
            'sortable' => $this->sortable,
            'addText' => $this->addText,
            'collapsedItemTemplate' => $this->template('item'),
            'maxItemCount' => $this->maxItemCount,
            'bulkUploadField' => $this->allowBulkUpload ? $this->bulkUploadItemFieldKey : null,
            'bulkUploadLimit' => $this->bulkUploadFileCountLimit,
            'itemIdAttribute' => $this->itemIdAttribute,
            'itemFields' => collect($this->itemFields)
                ->map(function ($field) {
                    return $field->toArray();
                })
                ->keyBy('key')
                ->all(),
        ]);
    }
}
