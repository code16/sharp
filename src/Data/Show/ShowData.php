<?php

namespace Code16\Sharp\Data\Show;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\InstanceAuthorizationsData;
use Code16\Sharp\Data\PageAlertData;
use Code16\Sharp\Data\Show\Fields\ShowFieldData;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

/**
 * @internal
 */
final class ShowData extends Data
{
    public function __construct(
        #[LiteralTypeScriptType('string | { [locale:string]: string } | null')]
        public string|array|null $title,
        public InstanceAuthorizationsData $authorizations,
        public ShowConfigData $config,
        #[LiteralTypeScriptType('{ [key:string]: ShowFieldData["value"] }')]
        public ?array $data,
        /** @var array<string,ShowFieldData> */
        public array $fields,
        public ShowLayoutData $layout,
        /** @var string[] */
        public ?array $locales,
        public ?PageAlertData $pageAlert = null,
    ) {}

    public static function from(array $show): self
    {
        return new self(
            title: $show['title'],
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
