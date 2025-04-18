<?php

namespace Code16\Sharp\Data\Embeds;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\Form\Fields\FormFieldData;
use Code16\Sharp\Data\Form\FormLayoutData;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

/**
 * @internal
 */
final class EmbedFormData extends Data
{
    public function __construct(
        #[LiteralTypeScriptType('{ [key:string]: FormFieldData["value"] }')]
        public ?array $data,
        /** @var array<string,FormFieldData> */
        public array $fields,
        public ?FormLayoutData $layout,
    ) {}

    public static function from(array $form): self
    {
        return new self(
            data: $form['data'],
            fields: FormFieldData::collection($form['fields']),
            layout: FormLayoutData::optional($form['layout']),
        );
    }
}
