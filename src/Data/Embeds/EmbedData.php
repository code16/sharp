<?php

namespace Code16\Sharp\Data\Embeds;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Data\Form\Fields\FormFieldData;

final class EmbedData extends Data
{
    public function __construct(
        public string $key,
        public string $label,
        public string $tag,
        /** @var string[] */
        public array $attributes,
        public string $template,
        /** @var DataCollection<string,FormFieldData> */
        public DataCollection $fields,
    ) {
    }

    public static function from(array $embed): self
    {
        $embed['fields'] = FormFieldData::collection($embed['fields']);

        return new self(...$embed);
    }
}
