<?php

namespace Code16\Sharp\Data;

final class LogoData extends Data
{
    public function __construct(
        public ?string $svg,
        public string $url,
    ) {}

    public static function from(array $icon): self
    {
        return new self(...$icon);
    }
}
