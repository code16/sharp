<?php


namespace Code16\Sharp\View\Components\Content\Utils;


use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;

class ComponentFragment extends Fragment
{
    public string $type = "component";

    protected string $name;
    protected array $attributes;

    public function __construct(string $name, array $attributes = [])
    {
        $this->name = $name;
        $this->attributes = $attributes;
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

        return new static(
            Str::after($element->tagName, 'x-'),
            $attributes
        );
    }
}
