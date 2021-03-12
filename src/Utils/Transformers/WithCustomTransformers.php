<?php

namespace Code16\Sharp\Utils\Transformers;

use Closure;
use Code16\Sharp\EntityList\Commands\Command;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Show\SharpShow;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * This trait allows a class to handle a custom transformers array.
 */
trait WithCustomTransformers
{
    protected array $transformers = [];

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
        if($this instanceof SharpForm || $this instanceof Command) {
            // It's a Form (full entity or from a Command), there's only one model.
            // We must add Form Field Formatters in the process
            return $this->applyFormatters(
                $this->applyTransformers($models)
            );
        }

        if($this instanceof SharpShow) {
            // It's a Show, there's only one model.
            return $this->applyTransformers($models, false);
        }

        // SharpEntityList case

        if($models instanceof LengthAwarePaginatorContract) {
            return new LengthAwarePaginator(
                $this->transform($models->items()),
                $models->total(),
                $models->perPage(),
                $models->currentPage()
            );
        }

        return collect($models)
            ->map(function($model) {
                return $this->applyTransformers($model);
            })
            ->all();
    }

    protected static function normalizeToSharpAttributeTransformer(Closure $closure): SharpAttributeTransformer
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
     * @param bool $forceFullObject if true all data keys of the model will be force set
     * @return array
     */
    protected function applyTransformers($model, bool $forceFullObject = true): array
    {
        $attributes = ArrayConverter::modelToArray($model);

        if($forceFullObject) {
            // Merge model attributes with form fields to be sure we have
            // all attributes which the front code needed.
            $attributes = collect($this->getDataKeys())
                ->flip()
                ->map(function () {
                    return null;
                })
                ->merge($attributes)
                ->all();
        }

        if (is_object($model)) {
            $attributes = $this->handleAutoRelatedAttributes($attributes, $model);
        }

        // Apply transformers
        foreach($this->transformers as $attribute => $transformer) {
            if(strpos($attribute, '[') !== false) {
                // List item case: apply transformer to each item
                $listAttribute = substr($attribute, 0, strpos($attribute, '['));
                $itemAttribute = substr($attribute, strpos($attribute, '[') + 1, -1);

                if(!array_key_exists($listAttribute, $attributes)) {
                    continue;
                }

                foreach ($model[$listAttribute] as $k => $itemModel) {
                    $attributes[$listAttribute][$k][$itemAttribute] = $transformer->apply(
                        $attributes[$listAttribute][$k][$itemAttribute] ?? null, $itemModel, $itemAttribute
                    );
                }

            } else {
                if(!isset($attributes[$attribute])) {
                    if(method_exists($transformer, 'applyIfAttributeIsMissing')
                        && !$transformer->applyIfAttributeIsMissing()) {
                        // The attribute is missing, and the transformer code specifically
                        // decide to be ignored in this case
                        continue;
                    }
                }
                
                $attributes[$attribute] = $transformer->apply(
                    $attributes[$attribute] ?? null, $model, $attribute
                );
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
        return collect($attributes)
            ->map(function ($value, $key) {
                $field = $this->findFieldByKey($key);
    
                return $field
                    ? $field->formatter()->toFront($field, $value)
                    : $value;
            })
            ->all();
    }

    /**
     * Handle `:` separator: we want to transform a related attribute in
     * a hasOne or belongsTo relationship. Ex: with "mother:name",
     * we add a transformed mother:name attribute in the array
     */
    protected function handleAutoRelatedAttributes(array $attributes, $model): array
    {
        collect($this->getDataKeys())
            ->filter(function ($key) {
                return strpos($key, ':') !== false;
            })
            ->map(function ($key) {
                return array_merge([$key], explode(':', $key));
            })
            ->each(function ($key) use (&$attributes, $model) {
                // For each one, we create a "relation:attribute" key
                // in the returned array
                $attributes[$key[0]] = $model->{$key[1]}->{$key[2]} ?? null;
            });

        return $attributes;
    }
}