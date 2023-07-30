<?php

namespace Code16\Sharp\Data\EntityList;

use Code16\Sharp\Data\CommandData;
use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Data\EntityStateData;
use Code16\Sharp\Data\Filters\FilterData;
use Code16\Sharp\Enums\CommandType;
use Spatie\TypeScriptTransformer\Attributes\Optional;
use Spatie\TypeScriptTransformer\Attributes\RecordTypeScriptType;

final class EntityListConfigData extends Data
{
    public function __construct(
        public string $instanceIdAttribute,
        public ?string $multiformAttribute,
        public bool $searchable,
        public bool $paginated,
        public bool $reorderable,
        public ?string $defaultSort,
        public ?string $defaultSortDir,
        public bool $hasShowPage,
        public string $deleteConfirmationText,
        public bool $deleteHidden,
        /** @var array<DataCollection<FilterData>> */
        public array $filters,
        #[RecordTypeScriptType(CommandType::class, 'array<DataCollection<'.CommandData::class.'>>')]
        public array $commands,
        public EntityStateData $state,
        #[Optional]
        public ?array $globalMessage = null,
    ) {
    }
    
    public static function from(array $config)
    {
        return new self(...[
            ...$config,
            'state' => EntityStateData::from($config['state']),
            'commands' => collect($config['commands'])
                ->map(fn (array $commands) => collect($commands)
                    ->map(fn (array $commands) => CommandData::collection($commands))
                )
                ->all(),
            'filters' => collect($config['filters'])
                ->map(fn (array $filters) => FilterData::collection($filters))
                ->all(),
        ]);
    }
}
