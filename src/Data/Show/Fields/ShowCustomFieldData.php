<?php

namespace Code16\Sharp\Data\Show\Fields;

use Code16\Sharp\Data\Data;
use Spatie\TypeScriptTransformer\Attributes\Optional;

/**
 * @internal
 */
final class ShowCustomFieldData extends Data
{
    #[Optional]
    public mixed $value;

    public function __construct(
        public string $key,
        public string $type,
        public bool $emptyVisible,
        ...$additionalOptions,
    ) {
        $this->additional($additionalOptions);
    }

    public static function from(array $field): self
    {
        return new self(...$field);
    }
}
