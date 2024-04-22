<?php

namespace Code16\Sharp\Data\EntityList;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Data\EntityAuthorizationsData;
use Code16\Sharp\Data\Filters\FilterValuesData;
use Code16\Sharp\Data\PageAlertData;
use Code16\Sharp\Data\PaginatorMetaData;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class EntityListData extends Data
{
    public function __construct(
        public EntityAuthorizationsData $authorizations,
        public EntityListConfigData $config,
        /** @var DataCollection<EntityListFieldData> */
        public DataCollection $fields,
        #[LiteralTypeScriptType('Array<{ [key: string]: any }>')]
        public array $data,
        /** @var DataCollection<string, EntityListMultiformData> */
        public DataCollection $forms,
        public FilterValuesData $filterValues,
        public ?PaginatorMetaData $meta = null,
        public ?PageAlertData $pageAlert = null,
    ) {
    }

    public static function from(array $entityList): self
    {
        return new self(
            authorizations: new EntityAuthorizationsData(...$entityList['authorizations']),
            config: EntityListConfigData::from($entityList['config']),
            fields: EntityListFieldData::collection($entityList['fields']),
            data: $entityList['data'],
            forms: EntityListMultiformData::collection($entityList['forms']),
            filterValues: FilterValuesData::from($entityList['filterValues']),
            meta: PaginatorMetaData::optional($entityList['meta'] ?? null),
            pageAlert: PageAlertData::optional($entityList['pageAlert'] ?? null),
        );
    }
}
