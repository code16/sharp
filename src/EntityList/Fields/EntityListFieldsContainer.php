<?php

namespace Code16\Sharp\EntityList\Fields;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;

class EntityListFieldsContainer
{
    use Conditionable;

    protected array $fields = [];

    final public function addField(IsEntityListField $field): self
    {
        $this->fields[] = $field;

        return $this;
    }

//    final public function addStateField(?Closure $callback = null): self
//    {
//        $field = tap(
//            new EntityListStateField(),
//            fn ($field) => $callback ? $callback($field) : null
//        );
//
//        $this->fields[] = $field;
//
//        return $this;
//    }
//
//    final public function addFilterField(string $filterKeyOrClassName, ?Closure $callback = null): self
//    {
//        $filterKey = class_exists($filterKeyOrClassName)
//            ? tap(app($filterKeyOrClassName), fn ($filter) => $filter->buildFilterConfig())->getKey()
//            : $filterKeyOrClassName;
//
//        $field = tap(
//            new EntityListFilterField($filterKey),
//            fn ($field) => $callback ? $callback($field) : null
//        );
//
//        $this->fields[] = $field;
//
//        return $this;
//    }

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
            $this->fields[] = EntityListStateField::make();
        }
        
        return collect($this->fields)
            ->map(fn (IsEntityListField $field) => $field->getFieldProperties());
    }
}
