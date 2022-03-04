<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\GeolocationFormatter;

class SharpFormGeolocationField extends SharpFormField
{
    const FIELD_TYPE = 'geolocation';

    protected string $displayUnit = 'DMS';
    protected bool $geocoding = false;
    protected int $zoomLevel = 10;
    protected ?array $boundaries = null;
    protected ?array $initialPosition = null;
    protected string $mapsProvider = 'gmaps';
    protected string $geocodingProvider = 'gmaps';
    protected array $mapsProviderOptions = [];
    protected array $geocodingProviderOptions = [];

    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE, new GeolocationFormatter());
    }

    public function setDisplayUnitDegreesMinutesSeconds(): self
    {
        $this->displayUnit = 'DMS';

        return $this;
    }

    public function setDisplayUnitDecimalDegrees(): self
    {
        $this->displayUnit = 'DD';

        return $this;
    }

    public function setGeocoding(bool $geocoding = true): self
    {
        $this->geocoding = $geocoding;

        return $this;
    }

    public function setApiKey(string $apiKey): self
    {
        return $this->setMapsApiKey($apiKey)
            ->setGeocodingApiKey($apiKey);
    }

    public function setMapsApiKey(string $apiKey): self
    {
        $this->mapsProviderOptions['apiKey'] = $apiKey;

        return $this;
    }

    public function setGeocodingApiKey(string $apiKey): self
    {
        $this->geocodingProviderOptions['apiKey'] = $apiKey;

        return $this;
    }

    public function setZoomLevel(int $zoomLevel): self
    {
        $this->zoomLevel = $zoomLevel;

        return $this;
    }

    public function setBoundaries(float $northEastLat, float $northEastLng, float $southWestLat, float $southWestLng): self
    {
        $this->boundaries = [
            'ne' => [
                'lat' => $northEastLat, 'lng' => $northEastLng,
            ],
            'sw' => [
                'lat' => $southWestLat, 'lng' => $southWestLng,
            ],
        ];

        return $this;
    }

    public function setMapsProvider(string $provider, array $options = []): self
    {
        $this->mapsProvider = $provider;
        $this->mapsProviderOptions += $options;

        return $this;
    }

    public function setGeocodingProvider(string $provider, array $options = []): self
    {
        $this->geocodingProvider = $provider;
        $this->geocodingProviderOptions += $options;

        return $this;
    }

    public function setInitialPosition(float $lat, float $lng): self
    {
        $this->initialPosition = compact('lat', 'lng');

        return $this;
    }

    public function clearInitialPosition(): self
    {
        $this->initialPosition = null;

        return $this;
    }

    public function clearBoundaries(): self
    {
        $this->boundaries = null;

        return $this;
    }

    protected function validationRules(): array
    {
        return [
            'geocoding'              => 'required|bool',
            'displayUnit'            => 'required|in:DD,DMS',
            'zoomLevel'              => 'int|min:0|max:25|required',
            'initialPosition'        => 'array|nullable',
            'boundaries'             => 'array|nullable',
            'mapsProvider.name'      => 'required|in:gmaps,osm',
            'geocodingProvider.name' => 'required|in:gmaps,osm',
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'geocoding'       => $this->geocoding,
            'displayUnit'     => $this->displayUnit,
            'zoomLevel'       => $this->zoomLevel,
            'initialPosition' => $this->initialPosition,
            'boundaries'      => $this->boundaries,
            'mapsProvider'    => [
                'name'    => $this->mapsProvider,
                'options' => $this->mapsProviderOptions,
            ],
            'geocodingProvider' => [
                'name'    => $this->geocodingProvider,
                'options' => $this->geocodingProviderOptions,
            ],
        ]);
    }
}
