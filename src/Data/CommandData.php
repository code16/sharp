<?php

namespace Code16\Sharp\Data;

use Code16\Sharp\Enums\CommandType;
use Code16\Sharp\Enums\InstanceSelectionMode;
use Spatie\TypeScriptTransformer\Attributes\Optional;

class CommandData extends Data
{
    public function __construct(
        public string $key,
        public ?string $label,
        public ?string $description,
        public CommandType $type,
        public ?string $confirmation,
        public ?string $modal_title,
        public ?string $modal_confirm_label,
        public bool $has_form,
        /** @var array<string|int>|bool */
        public array|bool $authorization,
        #[Optional]
        public ?InstanceSelectionMode $instance_selection = null
    ) {
    }
    
    public static function from(array $command): self
    {
        return new self(...[
            ...$command,
            'type' => CommandType::from($command['type']),
            'instance_selection' => ($command['instance_selection'] ?? null)
                ? InstanceSelectionMode::from($command['instance_selection'])
                : null,
        ]);
    }
}
