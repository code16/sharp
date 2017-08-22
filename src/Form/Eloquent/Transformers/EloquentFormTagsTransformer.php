<?php

namespace Code16\Sharp\Form\Eloquent\Transformers;

use Closure;
use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;

class EloquentFormTagsTransformer implements SharpAttributeTransformer
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

        $this->labelAttribute = $labelAttribute instanceof Closure
            ? new class($labelAttribute) {
                private $closure;
                function __construct($closure) {
                    $this->closure = $closure;
                }
                function apply($instance) {
                    return call_user_func($this->closure, $instance);
                }
            }
            : $labelAttribute;
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
        return $instance->$attribute
            ->map(function($item) {
                return [
                    "id" => $item->{$this->idAttribute},
                    "label" => is_object($this->labelAttribute)
                        ? $this->labelAttribute->apply($item)
                        : $item->{$this->labelAttribute}
                ];
            })->all();
    }
}