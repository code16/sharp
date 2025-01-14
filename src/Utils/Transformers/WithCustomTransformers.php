<?php

namespace Code16\Sharp\Utils\Transformers;

use Closure;
use Code16\Sharp\EntityList\Commands\Command;
use Code16\Sharp\Form\Fields\Editor\Uploads\FormEditorUploadForm;
use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Show\SharpShow;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\AbstractPaginator;

/**
 * This trait allows a class to handle a custom transformers array.
 */
trait WithCustomTransformers
{
    protected array $transformers = [];

    public function setCustomTransformer(string $attribute, string|Closure|SharpAttributeTransformer $transformer): self
    {
        if (! $transformer instanceof SharpAttributeTransformer) {
            $transformer = $transformer instanceof Closure
                ? $this->normalizeToSharpAttributeTransformer($transformer)
                : app($transformer);
        }

        $this->transformers[$attribute] = $transformer;

        return $this;
    }

    /**
     * Transforms a model or a models collection into an array.
     */
    public function transform($models): array|AbstractPaginator
    {
        if (
            $this instanceof SharpForm
            || $this instanceof Command
            || $this instanceof SharpFormEditorEmbed
            || $this instanceof FormEditorUploadForm
        ) {
            // Form case: there's only one model
            return $this->applyTransformers($models);
        }

        if ($this instanceof SharpShow) {
            // Show case: there's only one model
            return $this->applyTransformers(model: $models, forceFullObject: false);
        }

        // SharpEntityList case: collection of models (potentially paginated)

        $this->cacheEntityListInstances(
            match (true) {
                $models instanceof AbstractPaginator => $models->items(),
                $models instanceof Arrayable => $models->all(),
                default => $models,
            }
        );

        if ($models instanceof AbstractPaginator) {
            return $models->setCollection(
                collect($this->transform($models->items()))
            );
        }

        return collect($models)
            ->map(fn ($model) => $this->applyTransformers($model))
            ->all();
    }

    protected static function normalizeToSharpAttributeTransformer(Closure $closure): SharpAttributeTransformer
    {
        return new class($closure) implements SharpAttributeTransformer
        {
            public function __construct(private Closure $closure) {}

            public function apply($value, $instance = null, $attribute = null)
            {
                return call_user_func($this->closure, $value, $instance, $attribute);
            }
        };
    }

    protected function applyTransformers(array|object $model, bool $forceFullObject = true): array
    {
        $attributes = ArrayConverter::modelToArray($model);

        if ($forceFullObject) {
            // Merge model attributes with form fields to be sure we have
            // all attributes which the front code needed.
            $attributes = collect($this->getDataKeys())
                ->flip()
                ->map(fn () => null)
                ->merge($attributes)
                ->all();
        }

        if (is_object($model)) {
            $attributes = $this->handleAutoRelatedAttributes($attributes, $model);
        }

        // Apply transformers
        foreach ($this->transformers as $attribute => $transformer) {
            if (str_contains($attribute, '[')) {
                // List item case: apply transformer to each item
                $listAttribute = substr($attribute, 0, strpos($attribute, '['));
                $itemAttribute = substr($attribute, strpos($attribute, '[') + 1, -1);

                if (! array_key_exists($listAttribute, $attributes)) {
                    continue;
                }

                $listModel = $model;
                if (($sep = strpos($listAttribute, ':')) !== false) {
                    $listModel = $model[substr($listAttribute, 0, $sep)];
                    $listAttribute = substr($listAttribute, $sep + 1);
                }

                foreach ($listModel[$listAttribute] as $k => $itemModel) {
                    $attributes[$listAttribute][$k][$itemAttribute] = $transformer->apply(
                        $attributes[$listAttribute][$k][$itemAttribute] ?? null, $itemModel, $itemAttribute,
                    );
                }
            } else {
                if (! isset($attributes[$attribute])) {
                    if (method_exists($transformer, 'applyIfAttributeIsMissing')
                        && ! $transformer->applyIfAttributeIsMissing()) {
                        // The attribute is missing and the transformer code declared to be ignored in this case
                        continue;
                    }
                }

                if (($sep = strpos($attribute, ':')) !== false) {
                    $attributes[$attribute] = $transformer->apply(
                        $attributes[$attribute] ?? null,
                        $model[substr($attribute, 0, $sep)] ?? null,
                        substr($attribute, $sep + 1),
                    );

                    continue;
                }

                $attributes[$attribute] = $transformer->apply(
                    $attributes[$attribute] ?? null,
                    $model,
                    $attribute,
                );
            }
        }

        return $attributes;
    }

    /**
     * Handle `:` separator: we want to transform a related attribute in
     * a hasOne or belongsTo relationship. Ex: with "mother:name",
     * we add a transformed mother:name attribute in the array.
     */
    protected function handleAutoRelatedAttributes(array $attributes, $model): array
    {
        collect($this->getDataKeys())
            ->filter(fn (string $key) => str_contains($key, ':'))
            ->map(fn (string $key) => array_merge([$key], explode(':', $key)))
            ->each(function (array $key) use (&$attributes, $model) {
                // For each one, we create a "relation:attribute" key in the returned array
                $attributes[$key[0]] = $model->{$key[1]}->{$key[2]} ?? null;
            });

        return $attributes;
    }

    private function cacheEntityListInstances(array $instances): void
    {
        $idAttr = $this->instanceIdAttribute;

        sharp()->context()->cacheListInstances(
            collect($instances)
                ->filter(fn ($instance) => (((object) $instance)->$idAttr ?? null) !== null)
                ->mapWithKeys(fn ($instance) => [
                    ((object) $instance)->$idAttr => $instance,
                ])
        );
    }
}
