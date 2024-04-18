<?php

namespace Code16\Sharp\EntityList\Fields;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;

class EntityListFieldsContainer
{
    use Conditionable;

    protected array $fields = [];

    final public function addField(EntityListField $field): self
    {
        $this->fields[] = $field;

        return $this;
    }

    final public function addStateField(?string $label = null): self
    {
        $this->fields[] = new EntityListStateField($label ?? '');

        return $this;
    }

    public function setWidthOfField(string $fieldKey, ?int $width, int|bool|null $widthOnSmallScreens): self
    {
        if ($width !== null && ($field = collect($this->fields)->firstWhere('key', $fieldKey))) {
            if ($width) {
                $field->setWidth($width);
            } else {
                $field->setWidthFill();
            }

            if ($widthOnSmallScreens === false) {
                $field->hideOnSmallScreens();
            } elseif ($widthOnSmallScreens !== null) {
                $field->setWidthOnSmallScreens($widthOnSmallScreens);
            } else {
                $field->setWidthOnSmallScreensFill();
            }
        }

        return $this;
    }

    final public function getFields(bool $shouldHaveStateField = false): Collection
    {
        if($shouldHaveStateField
            && !collect($this->fields)->whereInstanceOf(EntityListStateField::class)->first()
        ) {
            $this->addStateField();
        }
        
        return collect($this->fields)
            ->map(fn (IsEntityListField $field) => $field->getFieldProperties());
    }
}
