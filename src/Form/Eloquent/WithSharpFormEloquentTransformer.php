<?php

namespace Code16\Sharp\Form\Eloquent;

use Closure;
use Code16\Sharp\Form\Transformers\SharpAttributeTransformer;

trait WithSharpFormEloquentTransformer
{
    /**
     * @var array
     */
    protected $transformers = [];

    /**
     * @param string $attribute
     * @param string|Closure $transformer
     * @return $this
     */
    function setCustomTransformer(string $attribute, $transformer)
    {
        if($transformer instanceof Closure) {
            // Normalize transformer to a regular SharpAttributeTransformer instance
            $transformer = new class($transformer) implements SharpAttributeTransformer {
                private $closure;

                function __construct($closure)
                {
                    $this->closure = $closure;
                }

                function apply($instance, string $attribute)
                {
                    return call_user_func($this->closure, $instance);
                }
            };

        } else {
            // Class name given; get an instance
            $transformer = app($transformer);
        }

        $this->transformers[$attribute] = $transformer;

        return $this;
    }

    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @param $model
     * @return array
     */
    function transform($model): array
    {
        $array = $model->toArray();

        // Apply transformers
        foreach($this->transformers as $attribute => $transformer) {
            $array[$attribute] = $transformer->apply($model, $attribute);
        }

        return $array;
    }
}