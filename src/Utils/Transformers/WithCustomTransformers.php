<?php

namespace Code16\Sharp\Utils\Transformers;

use Closure;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * This trait allows a class to handle a custom transformers array.
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
        if(!$transformer instanceof SharpAttributeTransformer) {
            $transformer = $transformer instanceof Closure
                ? $this->normalizeToSharpAttributeTransformer($transformer)
                : app($transformer);
        }

        $this->transformers[$attribute] = $transformer;

        return $this;
    }

    /**
     * Transforms a model or a models collection into an array.
     *
     * @param $models
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
                return $this->applyTransformers($model);
            })->all();
        }

        // Only one model, it's a Form: we must add
        // Form Field Formatters in the process
        return $this->applyFormatters(
            $this->applyTransformers($models)
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

            function apply($value, $instance = null, $attribute = null)
            {
                return call_user_func($this->closure, $value, $instance, $attribute);
            }
        };
    }

    /**
     * @param array|object $model the base model (Eloquent for instance), or an array of attributes
     * @param bool $forceAttributesPresence
     * @return array
     */
    protected function applyTransformers($model, bool $forceAttributesPresence = true)
    {
        $attributes = is_array($model) ? $model : $model->toArray();

        if($forceAttributesPresence) {
            // Merge model attribute with form fields to be sure we have
            // all attributes which the front code needed.
            $attributes = array_merge(
                collect($this->getDataKeys())->flip()->map(function () {
                    return null;
                })->all(), $attributes);

            if (is_object($model)) {
                $attributes = $this->handleAutoRelatedAttributes($attributes, $model);
            }
        }

        // Apply transformers
        foreach($this->transformers as $attribute => $transformer) {
            if(strpos($attribute, '[') !== false) {
                // List item case: apply transformer to each item
                $listAttribute = substr($attribute, 0, strpos($attribute, '['));
                $itemAttribute = substr($attribute, strpos($attribute, '[') + 1, -1);

                if(!isset($attributes[$listAttribute])) {
                    continue;
                }

                foreach ($model->$listAttribute as $k => $itemModel) {
                    $attributes[$listAttribute][$k][$itemAttribute] = $transformer->apply(
                        $attributes[$listAttribute][$k][$itemAttribute], $itemModel, $itemAttribute
                    );
                }

            } else {

                if(!isset($attributes[$attribute])) {
                    continue;
                }

                $attributes[$attribute] = $transformer->apply($attributes[$attribute], $model, $attribute);
            }
        }

        return $attributes;
    }

    /**
     * @param $attributes
     * @return array
     */
    protected function applyFormatters($attributes): array
    {
        return collect($attributes)->map(function ($value, $key) {
            $field = $this->findFieldByKey($key);

            return $field
                ? $field->formatter()->toFront($field, $value)
                : $value;
        })->all();
    }

    /**
     * Handle `:` separator: we want to transform a related attribute in
     * a hasOne or belongsTo relationship. Ex: with "mother:name",
     * we add a transformed mother:name attribute in the array
     *
     * @param $model
     * @return mixed
     */
    protected function handleAutoRelatedAttributes($attributes, $model)
    {
        collect($this->getDataKeys())
            ->filter(function ($key) {
                return strpos($key, ':') !== false;

            })->map(function ($key) {
                return array_merge([$key], explode(':', $key));

            })->each(function ($key) use (&$attributes, $model) {
                // For each one, we create a "relation:attribute" key
                // in the returned array
                $attributes[$key[0]] = $model->{$key[1]} ? $model->{$key[1]}->{$key[2]} : null;
            });

        return $attributes;
    }
}