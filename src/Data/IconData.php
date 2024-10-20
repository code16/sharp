<?php

namespace Code16\Sharp\Data;


final class IconData extends Data
{
    public function __construct(
        public ?string $svg,
        public ?string $name = null,
    ) {
    }

    public static function from(array $icon): self
    {
        return new self(...$icon);
    }
}
