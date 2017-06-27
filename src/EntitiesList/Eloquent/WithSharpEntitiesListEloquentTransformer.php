<?php

namespace Code16\Sharp\EntitiesList\Eloquent;

use Code16\Sharp\EntitiesList\Eloquent\Transformers\EloquentEntitiesListUploadTransformer;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait WithSharpEntitiesListEloquentTransformer
{
    use WithCustomTransformers;

    /**
     * Transforms a Model collection into an array.
     *
     * @param Collection|array|LengthAwarePaginatorContract $models
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

        $models = $models instanceof Collection ? $models : collect($models);

        return $models->map(function($model) {
            return $this->applyTransformers(
                collect($this->getDataContainersKeys()), $model, $model->toArray()
            );
        })->all();
    }

    /**
     * @param string $attribute
     * @param int|null $width
     * @param int|null $height
     * @param array $filters
     * @return $this
     */
    function setUploadTransformer(string $attribute, int $width = null, int $height = null, array $filters = [])
    {
        $this->transformers[$attribute] = new EloquentEntitiesListUploadTransformer(
            $attribute, $width, $height, $filters
        );

        return $this;
    }
}