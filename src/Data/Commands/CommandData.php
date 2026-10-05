<?php

namespace Code16\Sharp\Data\Commands;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\IconData;
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
        public ?IconData $icon,
        /** @var array<string|int>|bool */
        public array|bool $authorization,
        public ?InstanceSelectionMode $instanceSelection = null,
        public ?bool $primary = null,
    ) {}

    /**
     * @param  array{key: string, label: string|null, description: string|null, type: int|string, confirmation: array|null, hasForm: bool, icon: array|null, authorization: array<string|int>|bool, instanceSelection?: int|string|null, primary?: bool|null}  $command
     */
    public static function from(array $command): self
    {
        $command = [
            ...$command,
            'type' => CommandType::from($command['type']),
            'instanceSelection' => isset($command['instanceSelection'])
                ? InstanceSelectionMode::from($command['instanceSelection'])
                : null,
            'icon' => IconData::optional($command['icon'] ?? null),
        ];

        return new self(...$command);
    }
}
