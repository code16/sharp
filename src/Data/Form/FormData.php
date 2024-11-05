<?php

namespace Code16\Sharp\Data\Form;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Data\Form\Fields\FormFieldData;
use Code16\Sharp\Data\InstanceAuthorizationsData;
use Code16\Sharp\Data\PageAlertData;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class FormData extends Data
{
    public function __construct(
        public InstanceAuthorizationsData $authorizations,
        public FormConfigData $config,
        #[LiteralTypeScriptType('{ [key:string]: FormFieldData["value"] }')]
        public ?array $data,
        /** @var DataCollection<string,FormFieldData> */
        public DataCollection $fields,
        public FormLayoutData $layout,
        /** @var array<string> */
        public array $locales,
        public ?PageAlertData $pageAlert = null,
    ) {}

    public static function from(array $form): self
    {
        return new self(
            authorizations: InstanceAuthorizationsData::from($form['authorizations']),
            config: FormConfigData::from($form['config']),
            data: $form['data'],
            fields: FormFieldData::collection($form['fields']),
            layout: FormLayoutData::from($form['layout']),
            locales: $form['locales'],
            pageAlert: PageAlertData::optional($form['pageAlert']),
        );
    }
}
