<?php

namespace Code16\Sharp\View\Utils\Content;

use Illuminate\Support\Str;

class ComponentFragment extends Fragment
{
    public string $type = 'component';

    public function __construct(
        public string $name,
        public array $attributes = [],
        public string $content = ''
    ) {
    }

    public function getComponentName(): string
    {
        return $this->name;
    }

    public function getComponentAttributes(): array
    {
        return $this->attributes;
    }

    public static function fromDOMElement(\DOMElement $element): static
    {
        $attributes = collect($element->attributes)
            ->mapWithKeys(fn ($attr) => [
                $attr->name => $attr->value,
            ])
            ->toArray();

        $content = '';
        foreach ($element->childNodes as $childNode) {
            $content .= $childNode->ownerDocument->saveHTML($childNode);
        }

        return new static(
            Str::after($element->tagName, 'x-'),
            $attributes,
            $content,
        );
    }
}
