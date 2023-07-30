<?php

namespace Code16\Sharp\Data;

use Code16\Sharp\Enums\PageAlertLevel;

final class PageAlertConfigData extends Data
{
    public function __construct(
        public string $fieldKey,
        public ?PageAlertLevel $alertLevel,
    ) {
    }
    
    public static function from(array $alertConfig): self
    {
        return new self(
            fieldKey: $alertConfig['fieldKey'],
            alertLevel: $alertConfig['alertLevel']
                ? PageAlertLevel::from($alertConfig['alertLevel'])
                : null,
        );
    }
}
