<?php

namespace Code16\Sharp\Data\Commands;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\CommandType;
use Code16\Sharp\Enums\InstanceSelectionMode;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

/**
 * @internal
 */
final class CommandData extends Data
{
    public function __construct(
        public string $key,
        public ?string $label,
        public ?string $description,
        public CommandType $type,
        #[LiteralTypeScriptType('{ text: string, title: string | null, buttonLabel: string | null } | null')]
        public ?array $confirmation,
        public bool $hasForm,
        /** @var array<string|int>|bool */
        public array|bool $authorization,
        public ?InstanceSelectionMode $instanceSelection = null,
        public ?bool $primary = null,
    ) {}

    public static function from(array $command): self
    {
        $command = [
            ...$command,
            'type' => CommandType::from($command['type']),
            'instanceSelection' => isset($command['instanceSelection'])
                ? InstanceSelectionMode::from($command['instanceSelection'])
                : null,
        ];

        return new self(...$command);
    }
}
