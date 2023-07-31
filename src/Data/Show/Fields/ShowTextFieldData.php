<?php

namespace Code16\Sharp\Data\Show\Fields;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Data\EmbedData;
use Code16\Sharp\Enums\ShowFieldType;

final class ShowTextFieldData extends Data
{
   public function __construct(
       public string $key,
       public ShowFieldType $type,
       public bool $emptyVisible,
       public bool $html,
       public ?bool $localized = null,
       public ?int $collapseToWordCount = null,
       /** @var DataCollection<string, EmbedData> */
       public ?DataCollection $embeds = null,
       public ?string $label = null,
   ) {
   }

   public static function from(array $field): self
   {
       $field = [
           ...$field,
           'embeds' => isset($field['embeds'])
               ? EmbedData::collection($field['embeds'])
               : null,
       ];

       return new self(...$field);
   }
}
