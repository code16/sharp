<?php

namespace Code16\Sharp\Data\Show;


use Code16\Sharp\Data\Commands\CommandData;
use Code16\Sharp\Data\Commands\ConfigCommandsData;
use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\EntityStateData;
use Code16\Sharp\Enums\CommandType;
use Spatie\TypeScriptTransformer\Attributes\Optional;
use Spatie\TypeScriptTransformer\Attributes\RecordTypeScriptType;

final class ShowConfigData extends Data
{
    public function __construct(
        public string $deleteConfirmationText,
        public bool $isSingle = false,
        #[Optional]
        public ?ConfigCommandsData $commands = null,
        #[Optional]
        public ?string $multiformAttribute = null,
        #[Optional]
        public ?string $titleAttribute = null,
        #[Optional]
        public ?string $breadcrumbAttribute = null,
        #[Optional]
        public ?EntityStateData $state = null,
    ) {
    }

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
