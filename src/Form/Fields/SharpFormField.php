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
        $this->key = $key;
        $this->type = $type;
    }

    /**
     * @param string $label
     * @return SharpFormField
     */
    public function setLabel(string $label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @param string $helpMessage
     * @return SharpFormField
     */
    public function setHelpMessage(string $helpMessage)
    {
        $this->helpMessage = $helpMessage;

        return $this;
    }

    /**
     * @param string $conditionalDisplay
     * @return SharpFormField
     */
    public function setConditionalDisplay(string $conditionalDisplay)
    {
        $this->conditionalDisplay = $conditionalDisplay;

        return $this;
    }

    /**
     * @param bool $readOnly
     * @return SharpFormField
     */
    public function setReadOnly(bool $readOnly = true)
    {
        $this->readOnly = $readOnly;

        return $this;
    }

    /**
     * @param string $style
     * @return SharpFormField
     */
    public function setExtraStyle(string $style)
    {
        $this->extraStyle = $style;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            "key" => $this->key,
            "label" => $this->label
        ];
    }
}