<?php

namespace Code16\Sharp\Form\Transformers;

use Closure;
use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;

class FormTagsTransformer implements SharpAttributeTransformer
{
    /**
     * @var string
     */
    protected $idAttribute;

    /**
     * @var string|SharpAttributeTransformer
     */
    protected $labelAttribute;

    /**
     * EloquentTagsTransformer constructor.
     * @param string|Closure $labelAttribute
     * @param string $idAttribute
     */
    public function __construct($labelAttribute, string $idAttribute = "id")
    {
        $this->idAttribute = $idAttribute;
        $this->labelAttribute = $labelAttribute;
    }

    /**
     * Transform a model attribute to array (json-able).
     *
     * @param $instance
     * @param string $attribute
     * @return mixed
     */
    function apply($instance, string $attribute)
    {
        return collect($instance->$attribute)
            ->map(function($item) {
                return [
                    "id" => $item->{$this->idAttribute},
                    "label" => is_callable($this->labelAttribute)
                        ? call_user_func($this->labelAttribute, $item)
                        : $item->{$this->labelAttribute}
                ];
            })->all();
    }
}