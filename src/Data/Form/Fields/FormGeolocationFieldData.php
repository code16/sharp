<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\Form\Fields\Common\FormConditionalDisplayData;
use Code16\Sharp\Enums\FormFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

final class FormGeolocationFieldData extends Data
{
    #[Optional]
    #[LiteralTypeScriptType('{ lat: number, lng: number } | null')]
    public ?array $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.FormFieldType::Geolocation->value.'"')]
        public FormFieldType $type,
        public bool $geocoding,
        #[LiteralTypeScriptType('"DD" | "DMS"')]
        public string $displayUnit,
        public int $zoomLevel,
        #[LiteralTypeScriptType('{ name: "gmaps", options: { apiKey: string, mapId: string } } | { name: "osm", options?: { tilesUrl: string } }')]
        public array $mapsProvider,
        #[LiteralTypeScriptType('{ name: "gmaps", options: { apiKey: string } } | { name: "osm" }')]
        public array $geocodingProvider,
        #[TypeScriptType(['lng' => 'float', 'lat' => 'float'])]
        public ?array $initialPosition = null,
        #[TypeScriptType([
            'ne' => ['lat' => 'float', 'lng' => 'float'],
            'sw' => ['lat' => 'float', 'lng' => 'float'],
        ])]
        public ?array $boundaries = null,
        public ?string $label = null,
        public ?bool $readOnly = null,
        public ?FormConditionalDisplayData $conditionalDisplay = null,
        public ?string $helpMessage = null,
        public ?string $extraStyle = null,
    ) {}

    public static function from(array $field): self
    {
        return new self(...$field);
    }
}
