<?php

namespace Code16\Sharp\Data\Show;

use Code16\Sharp\Data\Commands\ConfigCommandsData;
use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\EntityStateData;

/**
 * @internal
 */
final class ShowConfigData extends Data
{
    public function __construct(
        public string $deleteConfirmationText,
        public string $formEditUrl,
        public bool $isSingle = false,
        public ?ConfigCommandsData $commands = null,
        public ?string $titleAttribute = null,
        public ?string $editButtonLabel = null,
        public ?EntityStateData $state = null,
    ) {}

    public static function from(array $config): self
    {
        $config = [
            ...$config,
            'state' => EntityStateData::optional($config['state'] ?? null),
            'commands' => ConfigCommandsData::optional($config['commands'] ?? null),
        ];

        return new self(...$config);
    }
}
