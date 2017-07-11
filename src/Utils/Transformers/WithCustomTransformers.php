<?php

namespace Code16\Sharp\Utils\Transformers;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * This trait allows a class to handle a custom transformers array
 *
 * Trait WithCustomTransformers
 * @package Code16\Sharp\Utils\Transformers
 */
trait WithCustomTransformers
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
     * Transforms a model or a models collection into an array.
     *
     * @param Collection|array|LengthAwarePaginatorContract|stdClass $models
     * @return array|LengthAwarePaginator
     */
    function transform($models)
    {
        if($models instanceof LengthAwarePaginatorContract) {
            return new LengthAwarePaginator(
                $this->transform($models->items()),
                $models->total(),
                $models->perPage(),
                $models->currentPage()
            );
        }

        if(is_array($models)) {
            $models = collect($models);
        }

        if($models instanceof Collection) {
            return $models->map(function($model) {
                return $this->applyTransformers(
                    collect($this->getDataKeys()), $model, $model->toArray()
                );
            })->all();
        }

        return $this->applyTransformers(
            collect($this->getDataKeys()), $models, $models->toArray()
        );
    }

    /**
     * @param Closure $closure
     * @return SharpAttributeTransformer
     */
    protected static function normalizeToSharpAttributeTransformer(Closure $closure)
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

    /**
     * @param Collection $keys keys which are meant to be in the final array
     * @param object $model the base model (Eloquent for instance)
     * @param array $array the initial model array representation
     * @return array
     */
    protected function applyTransformers(Collection $keys, $model, $array)
    {
        // Handle relation separator `:`
        $keys->filter(function ($key) {
            return strpos($key, ':') !== false;

        })->map(function ($key) {
            return array_merge([$key], explode(':', $key));

        })->each(function ($key) use (&$array, $model) {
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
}