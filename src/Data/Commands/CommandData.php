<?php

namespace Code16\Sharp\Data\Commands;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\CommandType;
use Code16\Sharp\Enums\InstanceSelectionMode;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class CommandData extends Data
{
    public function __construct(
        public string $key,
        public ?string $label,
        public ?string $description,
        public CommandType $type,
        #[LiteralTypeScriptType('{ title: string, description: string | null } | null')]
        public ?array $confirmation,
        public bool $has_form,
        /** @var array<string|int>|bool */
        public array|bool $authorization,
        public ?InstanceSelectionMode $instance_selection = null,
        public ?bool $primary = null,
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
