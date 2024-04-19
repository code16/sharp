<?php

namespace Code16\Sharp\EntityList\Fields;

use Code16\Sharp\Exceptions\SharpInvalidConfigException;

class EntityListField implements IsEntityListField
{
    public string $key;
    protected string $label = '';
    protected bool $sortable = false;
    protected bool $html = true;
    protected ?string $width = null;
    protected bool $hideOnXs = false;

    public static function make(string $key): self
    {
        $instance = new static();
        $instance->key = $key;

        return $instance;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function setSortable(bool $sortable = true): self
    {
        $this->sortable = $sortable;

        return $this;
    }

    public function setHtml(bool $html = true): self
    {
        $this->html = $html;

        return $this;
    }

    public function setWidth(int|string|float $width): self
    {
        $this->width = $this->normalizeWidthToPercentage($width);

        return $this;
    }

    public function setWidthFill(): self
    {
        $this->width = 'fill';

        return $this;
    }
    
    public function hideOnSmallScreens(bool $hideOnSmallScreens = true): self
    {
        $this->hideOnXs = $hideOnSmallScreens;
        
        return $this;
    }
    
    /**
     * @deprecated
     */
    public function setWidthOnSmallScreens(int $widthOnSmallScreens): self
    {
        return $this;
    }
    
    /**
     * @deprecated
     */
    public function setWidthOnSmallScreensFill(): self
    {
        return $this;
    }

    public function getFieldProperties(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'sortable' => $this->sortable,
            'html' => $this->html,
            'width' => $this->width,
            'hideOnXS' => $this->hideOnXs,
        ];
    }

    private function normalizeWidthToPercentage(string|int|float $width): string
    {
        if (is_int($width)) {
            throw_if(
                $width <= 0 || $width > 100,
                new SharpInvalidConfigException('Column width must be between 1 and 100 when an int is passed.')
            );

            return $width <= 12
                ? round($width / 12 * 100) . '%' // legacy 1-12 col width
                : $width . '%';
        }

        if (is_float($width)) {
            throw_if(
                $width <= 0 || $width > 1,
                new SharpInvalidConfigException('Column width must be between 0 and 1 when a float is passed.')
            );

            return ($width*100) . '%';
        }

        $width = str_replace(' ', '', $width);

        throw_if(
            ! preg_match('/((^100(\.0)?$)|(^([1-9]([0-9])?|0)(\.[0-9])?))%?$/', $width),
            new SharpInvalidConfigException('Column width must be defined as a percentage when a string is passed.')
        );

        return str($width)
            ->unless(str($width)->endsWith('%'), fn($str) => $str->append('%'))
            ->toString();
    }
}
