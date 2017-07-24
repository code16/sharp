<?php

namespace Code16\Sharp\Dashboard\Widgets;

use Code16\Sharp\Exceptions\Dashboard\SharpWidgetValidationException;
use Illuminate\Support\Facades\Validator;

abstract class SharpWidget
{

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $link;

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
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string $entityKey
     * @param string|null $instanceId
     * @return $this
     */
    public function setLink(string $entityKey, string $instanceId = null)
    {
        $this->link = $instanceId
            ? route("code16.sharp.api.form.edit", compact('entityKey', 'instanceId'))
            : route("code16.sharp.api.list", compact('entityKey'));

        return $this;
    }

    public function unsetLink()
    {
        $this->link = null;
    }

    /**
     * Throw an exception in case of invalid attribute value.
     *
     * @param array $properties
     * @throws SharpWidgetValidationException
     */
    protected function validate(array $properties)
    {
        $validator = Validator::make($properties, [
                'key' => 'required',
                'type' => 'required',
            ] + $this->validationRules());

        if ($validator->fails()) {
            throw new SharpWidgetValidationException($validator->errors());
        }
    }

    /**
     * @param array $childArray
     * @return array
     */
    protected function buildArray(array $childArray)
    {
        $array = collect([
            "key" => $this->key,
            "type" => $this->type,
            "title" => $this->title,
            "link" => $this->link
        ] + $childArray)
        ->filter(function($value) {
            return !is_null($value);
        })->all();

        $this->validate($array);

        return $array;
    }

    /**
     * Return specific validation rules.
     *
     * @return array
     */
    protected function validationRules()
    {
        return [];
    }

    /**
     * @return array
     */
    public abstract function toArray();
}