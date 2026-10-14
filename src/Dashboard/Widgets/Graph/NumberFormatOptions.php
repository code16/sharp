<?php

namespace Code16\Sharp\Dashboard\Widgets\Graph;

use Illuminate\Contracts\Support\Arrayable;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class NumberFormatOptions implements Arrayable
{
    /**
     * @see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Intl/NumberFormat/NumberFormat#options
     */
    public function __construct(
        public ?string $style = null,
        public ?string $currency = null,
        public ?string $currencyDisplay = null,
        public ?string $currencySign = null,
        public ?bool $useGrouping = null,
        public ?int $minimumIntegerDigits = null,
        public ?int $minimumFractionDigits = null,
        public ?int $maximumFractionDigits = null,
        public ?int $minimumSignificantDigits = null,
        public ?int $maximumSignificantDigits = null,
        public ?string $notation = null,
        public ?string $compactDisplay = null,
        public ?string $signDisplay = null,
        public ?string $unit = null,
        public ?string $unitDisplay = null,
        public ?string $roundingMode = null,
        public ?int $roundingPriority = null,
        public ?int $roundingIncrement = null,
        public ?int $trailingZeroDisplay = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
