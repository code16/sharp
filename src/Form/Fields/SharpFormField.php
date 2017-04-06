<?php

namespace Code16\Sharp\Form\Fields;

abstract class SharpFormField
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $helpMessage;

    /**
     * @var string
     */
    protected $conditionalDisplay;

    /**
     * @var bool
     */
    protected $readOnly;

    /**
     * @var string
     */
    protected $extraStyle;

    /**
     * @param string $key
     * @param string $type
     */
    protected function __construct(string $key, string $type)
    {
        if(!trim($key)) {
            throw new \InvalidArgumentException("A field key must be provided");
        }

        if(!trim($type)) {
            throw new \InvalidArgumentException("A field type must be provided");
        }

        $this->key = $key;
        $this->type = $type;
    }

    /**
     * @param string $label
     * @return static
     */
    public function setLabel(string $label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @param string $helpMessage
     * @return static
     */
    public function setHelpMessage(string $helpMessage)
    {
        $this->helpMessage = $helpMessage;

        return $this;
    }

    /**
     * @param string $conditionalDisplay
     * @return static
     */
    public function setConditionalDisplay(string $conditionalDisplay)
    {
        $this->conditionalDisplay = $conditionalDisplay;

        return $this;
    }

    /**
     * @param bool $readOnly
     * @return static
     */
    public function setReadOnly(bool $readOnly = true)
    {
        $this->readOnly = $readOnly;

        return $this;
    }

    /**
     * @param string $style
     * @return static
     */
    public function setExtraStyle(string $style)
    {
        $this->extraStyle = $style;

        return $this;
    }

    /**
     * Create the properties array for the field, using parent::makeArray()
     *
     * @return array
     */
    public abstract function toArray(): array;

    /**
     * @return array
     */
    protected function makeArray(array $childArray)
    {
        return collect([
            "key" => $this->key,
            "type" => $this->type,
            "label" => $this->label,
            "readOnly" => $this->readOnly,
            "conditionalDisplay" => $this->conditionalDisplay,
            "helpMessage" => $this->helpMessage,
            "extraStyle" => $this->extraStyle,
        ] + $childArray)
            ->filter(function($value) {
                return !is_null($value);
            })->all();
    }
}