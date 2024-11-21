<?php

namespace Code16\Sharp\Data\EntityList;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Data\EntityListAuthorizationsData;
use Code16\Sharp\Data\Filters\FilterValuesData;
use Code16\Sharp\Data\PageAlertData;
use Code16\Sharp\Data\PaginatorMetaData;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class EntityListData extends Data
{
    public function __construct(
        public EntityListAuthorizationsData $authorizations,
        public EntityListConfigData $config,
        /** @var DataCollection<EntityListFieldData> */
        public DataCollection $fields,
        #[LiteralTypeScriptType('Array<{ [key: string]: any }>')]
        public array $data,
        public FilterValuesData $filterValues,
        public ?EntityListQueryParamsData $query,
        /** @var DataCollection<string, EntityListMultiformData> */
        public ?DataCollection $forms = null,
        public ?PaginatorMetaData $meta = null,
        public ?PageAlertData $pageAlert = null,
    ) {}

    public static function from(array $entityList): self
    {
        return new self(
            authorizations: new EntityListAuthorizationsData(...$entityList['authorizations']),
            config: EntityListConfigData::from($entityList['config']),
            fields: EntityListFieldData::collection($entityList['fields']),
            data: $entityList['data'],
            filterValues: FilterValuesData::from($entityList['filterValues']),
            query: EntityListQueryParamsData::optional($entityList['query']),
            forms: $entityList['forms'] ? EntityListMultiformData::collection($entityList['forms']) : null,
            meta: PaginatorMetaData::optional($entityList['meta'] ?? null),
            pageAlert: PageAlertData::optional($entityList['pageAlert'] ?? null),
        );
    }
}
