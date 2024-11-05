<?php

namespace Code16\Sharp\Data\Embeds;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Data\Form\Fields\FormFieldData;
use Code16\Sharp\Data\IconData;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

final class EmbedData extends Data
{
    #[Optional]
    #[LiteralTypeScriptType('FormData["data"] & { slot: string, _html: string }')]
    public ?array $value;

    public function __construct(
        public string $key,
        public string $label,
        public string $tag,
        public ?IconData $icon,
        /** @var string[] */
        public array $attributes,
        public string $template,
        /** @var DataCollection<string,FormFieldData> */
        public DataCollection $fields,
    ) {}

    public static function from(array $embed): self
    {
        $embed['fields'] = FormFieldData::collection($embed['fields']);
        $embed['icon'] = IconData::optional($embed['icon']);

        return new self(...$embed);
    }
}
