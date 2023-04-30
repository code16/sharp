<?php

namespace Code16\Sharp\EntityList\Fields;

use Illuminate\Support\Collection;

class EntityListFieldsContainer
{
    protected array $fields = [];

    final public function addField(EntityListField $field): self
    {
        $this->fields[] = $field;

        return $this;
    }

    public function setWidthOfField(string $fieldKey, ?int $width, int|bool|null $widthOnSmallScreens): self
    {
        if($width !== null && ($field = collect($this->fields)->firstWhere('key', $fieldKey))) {
            if($width) {
                $field->setWidth($width);
            } else {
                $field->setWidthFill();
            }
            
            if($widthOnSmallScreens === false) {
                $field->hideOnSmallScreens();
            } elseif($widthOnSmallScreens !== null) {
                $field->setWidthOnSmallScreens($widthOnSmallScreens);
            } else {
                $field->setWidthOnSmallScreensFill();
            }
        }
        
        return $this;
    }

    final public function getFields(): Collection
    {
        return collect($this->fields)
            ->map(fn (EntityListField $field) => $field->getFieldProperties())
            ->keyBy('key');
    }

    final public function getLayout(): Collection
    {
        return collect($this->fields)
            ->map(fn (EntityListField $field) => $field->getLayoutProperties())
            ->values();
    }
}
