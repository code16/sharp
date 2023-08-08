<?php

namespace Code16\Sharp\Data\EntityList;

use Code16\Sharp\Data\Commands\CommandData;
use Code16\Sharp\Data\Commands\ConfigCommandsData;
use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\EntityStateData;
use Code16\Sharp\Data\Filters\ConfigFiltersData;
use Code16\Sharp\Data\PageAlertConfigData;
use Code16\Sharp\Enums\CommandType;
use Spatie\TypeScriptTransformer\Attributes\Optional;
use Spatie\TypeScriptTransformer\Attributes\RecordTypeScriptType;

final class EntityListConfigData extends Data
{
    public function __construct(
        public string  $instanceIdAttribute,
        public bool $searchable,
        public bool $paginated,
        public bool $reorderable,
        public ?string $defaultSort,
        public ?string $defaultSortDir,
        public bool $hasShowPage,
        public string  $deleteConfirmationText,
        public bool $deleteHidden,
        #[Optional]
        public ?ConfigFiltersData $filters = null,
        #[Optional]
        public ?ConfigCommandsData $commands = null,
        #[Optional]
        public ?string $multiformAttribute = null,
        #[Optional]
        public ?EntityStateData $state = null,
        #[Optional]
        public ?PageAlertConfigData $globalMessage = null,
    ) {
    }

    public static function from(array $config)
    {
        $config = [
            ...$config,
            'state' => isset($config['state'])
                ? EntityStateData::from($config['state'])
                : null,
            'commands' => isset($config['commands'])
                ? ConfigCommandsData::from($config['commands'])
                : null,
            'filters' => isset($config['filters'])
                ? ConfigFiltersData::from($config['filters'])
                : null,
            'globalMessage' => isset($config['globalMessage'])
                ? PageAlertConfigData::from($config['globalMessage'])
                : null,
        ];

        return new self(...$config);
    }
}
