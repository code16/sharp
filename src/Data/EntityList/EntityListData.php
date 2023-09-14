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
        public DataCollection $fields,
        public EntityListDataData $data,
        /** @var DataCollection<string, EntityListMultiformData> */
        public DataCollection $forms,
        #[Optional]
        public ?PaginatorMetaData $meta = null,
    ) {
    }

    public static function from(array $entityList): self
    {
        return new self(
            authorizations: new EntityAuthorizationsData(...$entityList['authorizations']),
            config: EntityListConfigData::from($entityList['config']),
            fields: EntityListFieldData::collection($entityList['fields']),
            data: EntityListDataData::from($entityList['data']),
            forms: EntityListMultiformData::collection($entityList['forms']),
            meta: isset($entityList['meta'])
                ? PaginatorMetaData::from($entityList['meta'])
                : null,
        );
    }
}
