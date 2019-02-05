<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\GeolocationFormatter;

class SharpFormGeolocationField extends SharpFormField
{
    const FIELD_TYPE = "geolocation";

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $displayUnit = "DMS";

    /**
     * @var bool
     */
    protected $geocoding = false;

    /**
     * @var int
     */
    protected $zoomLevel = 10;

    /**
     * @var array
     */
    protected $boundaries;

    /**
     * @var array
     */
    protected $initialPosition;

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        return new static($key, static::FIELD_TYPE, new GeolocationFormatter);
    }

    /**
     * @return $this
     */
    public function setDisplayUnitDegreesMinutesSeconds()
    {
        $this->displayUnit = "DMS";

        return $this;
    }

    /**
     * @return $this
     */
    public function setDisplayUnitDecimalDegrees()
    {
        $this->displayUnit = "DD";

        return $this;
    }

    /**
     * @param bool $geocoding
     * @return $this
     */
    public function setGeocoding(bool $geocoding = true)
    {
        $this->geocoding = $geocoding;

        return $this;
    }

    /**
     * @param string $apiKey
     * @return $this
     */
    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @param int $zoomLevel
     * @return $this
     */
    public function setZoomLevel(int $zoomLevel)
    {
        $this->zoomLevel = $zoomLevel;

        return $this;
    }

    /**
     * @param float $northEastLat
     * @param float $northEastLng
     * @param float $southWestLat
     * @param float $southWestLng
     * @return $this
     */
    public function setBoundaries(float $northEastLat, float $northEastLng, float $southWestLat, float $southWestLng)
    {
        $this->boundaries = [
            "ne" => [
                "lat" => $northEastLat, "lng" => $northEastLng
            ],
            "sw" => [
                "lat" => $southWestLat, "lng" => $southWestLng
            ]
        ];

        return $this;
    }

    /**
     * @param float $lat
     * @param float $lng
     * @return $this
     */
    public function setInitialPosition(float $lat, float $lng)
    {
        $this->initialPosition = compact('lat', 'lng');

        return $this;
    }

    /**
     * @return $this
     */
    public function clearInitialPosition()
    {
        $this->initialPosition = null;

        return $this;
    }

    /**
     * @return $this
     */
    public function clearBoundaries()
    {
        $this->boundaries = null;

        return $this;
    }

    /**
     * @return array
     */
    protected function validationRules()
    {
        return [
            "geocoding" => "required|bool",
            "apiKey" => "required_if:geocoding,1",
            "displayUnit" => "required|in:DD,DMS",
            "zoomLevel" => "int|min:0|max:25|required",
            "initialPosition" => "array|nullable",
            "boundaries" => "array|nullable"
        ];
    }

    /**
     * @return array
     * @throws \Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "geocoding" => $this->geocoding,
            "displayUnit" => $this->displayUnit,
            "apiKey" => $this->apiKey,
            "zoomLevel" => $this->zoomLevel,
            "initialPosition" => $this->initialPosition,
            "boundaries" => $this->boundaries,
        ]);
    }
}