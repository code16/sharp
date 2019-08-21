<?php

namespace Code16\Sharp\Show;

abstract class SharpSingleShow extends SharpShow
{
    /*
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @param $id
     * @return array
     */
    function find($id): array
    {
        return $this->findSingle();
    }

    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @return array
     */
    abstract function findSingle(): array;
}