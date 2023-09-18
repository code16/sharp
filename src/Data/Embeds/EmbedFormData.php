<?php

namespace Code16\Sharp\Data\Embeds;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Data\Form\Fields\FormFieldData;
use Code16\Sharp\Data\Form\FormConfigData;
use Code16\Sharp\Data\Form\FormLayoutData;
use Code16\Sharp\Data\InstanceAuthorizationsData;
use Code16\Sharp\Data\PageAlertData;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class EmbedFormData extends Data
{
    public function __construct(
        #[LiteralTypeScriptType('{ [key:string]: FormFieldData["value"] }')]
        public array $data,
        /** @var DataCollection<string,FormFieldData> */
        public DataCollection $fields,
        public FormLayoutData $layout,
    ) {
    }

    public static function from(array $form): self
    {
        return new self(
            data: $form['data'],
            fields: FormFieldData::collection($form['fields']),
            layout: FormLayoutData::from($form['layout']),
        );
    }
}
