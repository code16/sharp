<?php

namespace Code16\Sharp\Form;

interface SharpFormData
{
    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @param $id
     * @return array
     */
    function get($id): array;

    /**
     * @param $id
     * @param array $data
     * @return bool
     */
    function update($id, array $data): bool;

    /**
     * @param array $data
     * @return bool
     */
    function store(array $data): bool;

    /**
     * @param $id
     * @return bool
     */
    function delete($id): bool;
}