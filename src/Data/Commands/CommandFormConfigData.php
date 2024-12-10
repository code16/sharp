<?php

namespace Code16\Sharp\Data\Commands;

use Code16\Sharp\Data\Data;

/**
 * @internal
 */
final class CommandFormConfigData extends Data
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $buttonLabel = null,
        public bool $showSubmitAndReopenButton = false,
        public ?string $submitAndReopenButtonLabel = null,
    ) {}

    public static function from(array $config): self
    {
        return new self(...$config);
    }
}
