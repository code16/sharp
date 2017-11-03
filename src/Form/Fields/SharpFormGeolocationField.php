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
     * @return array
     */
    protected function validationRules()
    {
        return [
            "geocoding" => "required|bool",
            "apiKey" => "required_if:geocoding,1",
            "displayUnit" => "required|in:DD,DMS",
        ];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "geocoding" => $this->geocoding,
            "displayUnit" => $this->displayUnit,
            "apiKey" => $this->apiKey,
        ]);
    }
}