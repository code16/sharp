<?php

namespace Code16\Sharp\Data\EntityList;

use Code16\Sharp\Data\Commands\ConfigCommandsData;
use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\EntityStateData;
use Code16\Sharp\Data\Filters\ConfigFiltersData;

/**
 * @internal
 */
final class EntityListConfigData extends Data
{
    public function __construct(
        public string $instanceIdAttribute,
        public bool $searchable,
        public bool $reorderable,
        public ?string $defaultSort,
        public ?string $defaultSortDir,
        public bool $hasShowPage,
        public string $deleteConfirmationText,
        public bool $deleteHidden,
        public ?string $multiformAttribute,
        public ?string $createButtonLabel = null,
        public bool $quickCreationForm = false,
        public ?ConfigFiltersData $filters = null,
        public ?ConfigCommandsData $commands = null,
        public ?EntityStateData $state = null,
    ) {}

    public static function from(array $config)
    {
        $config = [
            ...$config,
            'state' => EntityStateData::optional($config['state'] ?? null),
            'commands' => ConfigCommandsData::optional($config['commands'] ?? null),
            'filters' => ConfigFiltersData::optional($config['filters'] ?? null),
        ];

        return new self(...$config);
    }
}
