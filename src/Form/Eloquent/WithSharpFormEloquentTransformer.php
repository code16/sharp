<?php

namespace Code16\Sharp\Form\Eloquent;

use Closure;
use Code16\Sharp\Form\Eloquent\Transformers\EloquentTagsTransformer;
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
        $transformer = $transformer instanceof Closure
            ? $this->normalizeToSharpAttributeTransformer($transformer)
            : app($transformer);

        $this->transformers[$attribute] = $transformer;

        return $this;
    }

    /**
     * @param string $attribute
     * @param string|Closure $labelAttribute
     * @param string $idAttribute
     * @return $this
     */
    function setTagsTransformer(string $attribute, $labelAttribute, $idAttribute = "id")
    {
        $transformer = new EloquentTagsTransformer($labelAttribute, $idAttribute);

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

        // Handle relation separator `:`
        collect($this->getFieldKeys())->filter(function($key) {
            return strpos($key, ':') !== false;

        })->map(function($key) {
            return array_merge([$key], explode(':', $key));

        })->each(function($key) use(&$array, $model) {
            // For each one, we create a "relation:attribute" key
            // in the returned array
            $array[$key[0]] = $model->{$key[1]} ? $model->{$key[1]}->{$key[2]} : null;
        });

        // Apply transformers
        foreach($this->transformers as $attribute => $transformer) {
            $array[$attribute] = $transformer->apply($model, $attribute);
        }

        return $array;
    }

    /**
     * @param Closure $closure
     * @return SharpAttributeTransformer
     */
    public static function normalizeToSharpAttributeTransformer(Closure $closure)
    {
        return new class($closure) implements SharpAttributeTransformer
        {
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
    }
}