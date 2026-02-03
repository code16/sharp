<?php

namespace Code16\Sharp\Utils\Transformers;

final class AttributePath
{
    public function __construct(
        public readonly string $key,
        public readonly bool $isList,
        public readonly ?string $relation,
        public readonly ?string $listAttribute,
        public readonly ?string $itemAttribute,
        public readonly string $attribute,
    ) {}

    public static function parse(string $attribute): self
    {
        if (str_contains($attribute, '[')) {
            $listAttribute = substr($attribute, 0, strpos($attribute, '['));
            $itemAttribute = substr($attribute, strpos($attribute, '[') + 1, -1);

            $relation = null;
            if (($sep = strpos($listAttribute, ':')) !== false) {
                $relation = substr($listAttribute, 0, $sep);
                $listAttribute = substr($listAttribute, $sep + 1);
            }

            return new self(
                key: $attribute,
                isList: true,
                relation: $relation,
                listAttribute: $listAttribute,
                itemAttribute: $itemAttribute,
                attribute: $itemAttribute,
            );
        }

        $relation = null;
        $attributeName = $attribute;
        if (($sep = strpos($attribute, ':')) !== false) {
            $relation = substr($attribute, 0, $sep);
            $attributeName = substr($attribute, $sep + 1);
        }

        return new self(
            key: $attribute,
            isList: false,
            relation: $relation,
            listAttribute: null,
            itemAttribute: null,
            attribute: $relation ? $attributeName : $attribute,
        );
    }

    public function isRelated(): bool
    {
        return $this->relation !== null;
    }
}
