<?php

namespace Code16\Sharp\Data\Show;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Data\InstanceAuthorizationsData;
use Code16\Sharp\Data\PageAlertData;
use Code16\Sharp\Data\Show\Fields\ShowFieldData;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class ShowData extends Data
{
    public function __construct(
        public InstanceAuthorizationsData $authorizations,
        public ShowConfigData $config,
        #[LiteralTypeScriptType('{ [key:string]: ShowFieldData["value"] }')]
        public array $data,
        /** @var DataCollection<string,ShowFieldData> */
        public DataCollection $fields,
        public ShowLayoutData $layout,
        /** @var string[] */
        public ?array $locales,
        public ?PageAlertData $pageAlert = null,
    ) {
    }

    public static function from(array $show): self
    {
        return new self(
            authorizations: InstanceAuthorizationsData::from($show['authorizations']),
            config: ShowConfigData::from($show['config']),
            data: $show['data'],
            fields: ShowFieldData::collection($show['fields']),
            layout: ShowLayoutData::from($show['layout']),
            locales: $show['locales'],
            pageAlert: PageAlertData::optional($show['pageAlert']),
        );
    }
}
