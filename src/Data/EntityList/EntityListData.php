<?php

namespace Code16\Sharp\Data\EntityList;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\EntityListAuthorizationsData;
use Code16\Sharp\Data\Filters\FilterValuesData;
use Code16\Sharp\Data\PageAlertData;
use Code16\Sharp\Data\PaginatorMetaData;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

/**
 * @internal
 */
final class EntityListData extends Data
{
    public function __construct(
        public ?string $title,
        public EntityListAuthorizationsData $authorizations,
        public EntityListConfigData $config,
        /** @var EntityListFieldData[] */
        public array $fields,
        #[LiteralTypeScriptType('Array<{ [key: string]: any, _meta: EntityListItemMeta }>')]
        public array $data,
        public FilterValuesData $filterValues,
        public ?EntityListQueryParamsData $query,
        /** @var EntityListEntityData[]|null */
        public ?array $entities = null,
        public ?PaginatorMetaData $meta = null,
        public ?PageAlertData $pageAlert = null,
    ) {}

    public static function from(array $entityList): self
    {
        return new self(
            title: $entityList['title'] ?? null,
            authorizations: new EntityListAuthorizationsData(...$entityList['authorizations']),
            config: EntityListConfigData::from($entityList['config']),
            fields: EntityListFieldData::collection($entityList['fields']),
            data: $entityList['data'],
            filterValues: FilterValuesData::from($entityList['filterValues']),
            query: EntityListQueryParamsData::optional($entityList['query']),
            entities: $entityList['entities']
                ? EntityListEntityData::collection($entityList['entities'])
                : null,
            meta: PaginatorMetaData::optional($entityList['meta'] ?? null),
            pageAlert: PageAlertData::optional($entityList['pageAlert'] ?? null),
        );
    }
}
