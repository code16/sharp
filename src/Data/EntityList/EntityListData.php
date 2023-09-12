<?php

namespace Code16\Sharp\Data\EntityList;


use Code16\Sharp\Data\BreadcrumbData;
use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Data\EntityAuthorizationsData;
use Code16\Sharp\Data\NotificationData;
use Code16\Sharp\Data\PaginatorMetaData;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

final class EntityListData extends Data
{
    public function __construct(
        public EntityAuthorizationsData $authorizations,
        public EntityListConfigData $config,
        /** @var DataCollection<string,EntityListFieldData> */
        public DataCollection $containers,
        public EntityListDataData $data,
        /** @var array<string,mixed> */
        public array $fields,
        /** @var DataCollection<string, EntityListMultiformData> */
        public DataCollection $forms,
        /** @var DataCollection<EntityListFieldLayoutData> */
        public DataCollection $layout,
        #[Optional]
        public ?PaginatorMetaData $meta = null,
    ) {
    }

    public static function from(array $entityList): self
    {
        return new self(
            authorizations: new EntityAuthorizationsData(...$entityList['authorizations']),
            config: EntityListConfigData::from($entityList['config']),
            containers: EntityListFieldData::collection($entityList['containers']),
            data: EntityListDataData::from($entityList['data']),
            fields: $entityList['fields'],
            forms: EntityListMultiformData::collection($entityList['forms']),
            layout: EntityListFieldLayoutData::collection($entityList['layout']),
            meta: isset($entityList['meta'])
                ? PaginatorMetaData::from($entityList['meta'])
                : null,
        );
    }
}
