<?php

namespace Code16\Sharp\Form;

use Code16\Sharp\Exceptions\SharpException;

abstract class SharpSingleForm extends SharpForm
{
    /**
     * @return array
     */
    public function formConfig()
    {
        return array_merge(
            parent::formConfig(),
            ["isSingle" => true]
        );
    }

    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @param $id
     * @return array
     */
    final function find($id): array
    {
        return $this->findSingle();
    }

    /**
     * @param $id
     * @param array $data
     * @return mixed the instance id
     */
    final function update($id, array $data)
    {
        return $this->updateSingle($data);
    }

    /**
     * @param $data
     * @throws SharpException
     */
    final public function storeInstance($data)
    {
        throw new SharpException("Store is not possible in a SingleSharpForm.");
    }

    /**
     * @param $id
     */
    final function delete($id)
    {
        $this->deleteSingle();
    }

    /**
     * @return array
     */
    abstract protected function findSingle();

    /**
     * @param array $data
     * @return mixed
     */
    abstract protected function updateSingle(array $data);

    abstract protected function deleteSingle();
}