<?php

namespace Code16\Sharp\Utils\Transformers;

final class TransformerPipeline
{
    /**
     * @param  array<string, SharpAttributeTransformer>  $transformers
     */
    public function __construct(private array $transformers) {}

    public function apply(array $attributes, array|object $model): array
    {
        foreach ($this->transformers as $attribute => $transformer) {
            $path = AttributePath::parse($attribute);

            if ($path->isList) {
                $attributes = $this->applyListTransformer($attributes, $model, $path, $transformer);

                continue;
            }

            $attributes = $this->applySingleTransformer($attributes, $model, $path, $transformer);
        }

        return $attributes;
    }

    private function applyListTransformer(
        array $attributes,
        array|object $model,
        AttributePath $path,
        SharpAttributeTransformer $transformer,
    ): array {
        if (! array_key_exists($path->listAttribute, $attributes)) {
            return $attributes;
        }

        $listModel = $model;
        if ($path->isRelated()) {
            $listModel = $model[$path->relation];
        }

        foreach ($listModel[$path->listAttribute] as $k => $itemModel) {
            $attributes[$path->listAttribute][$k][$path->itemAttribute] = $transformer->apply(
                $attributes[$path->listAttribute][$k][$path->itemAttribute] ?? null,
                $itemModel,
                $path->itemAttribute,
            );
        }

        return $attributes;
    }

    private function applySingleTransformer(
        array $attributes,
        array|object $model,
        AttributePath $path,
        SharpAttributeTransformer $transformer,
    ): array {
        if (! isset($attributes[$path->key])) {
            if (method_exists($transformer, 'applyIfAttributeIsMissing')
                && ! $transformer->applyIfAttributeIsMissing()) {
                // The attribute is missing and the transformer code declared to be ignored in this case
                return $attributes;
            }
        }

        if ($path->isRelated()) {
            $attributes[$path->key] = $transformer->apply(
                $attributes[$path->key] ?? null,
                $model[$path->relation] ?? null,
                $path->attribute,
            );

            return $attributes;
        }

        $attributes[$path->key] = $transformer->apply(
            $attributes[$path->key] ?? null,
            $model,
            $path->attribute,
        );

        return $attributes;
    }
}
