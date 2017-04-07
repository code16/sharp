<?php

namespace Code16\Sharp\Form\Eloquent;

trait WithSharpFormEloquentTransformer
{

    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @param $id
     * @return array
     */
    function find($id): array
    {
        return $this->findModel($id)->toArray();
    }
}