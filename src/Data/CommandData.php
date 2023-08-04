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
        public ?bool $primary = null,
        #[Optional]
        public ?InstanceSelectionMode $instance_selection = null
    ) {
    }

    public static function from(array $command): self
    {
        $command = [
            ...$command,
            'type' => CommandType::from($command['type']),
            'instance_selection' => isset($command['instance_selection'])
                ? InstanceSelectionMode::from($command['instance_selection'])
                : null,
        ];

        return new self(...$command);
    }
}
