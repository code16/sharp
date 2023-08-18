<?php

namespace Code16\Sharp\Form;

use Code16\Sharp\Exceptions\SharpException;

abstract class SharpSingleForm extends SharpForm
{
    final public function formConfig(): array
    {
        return array_merge(
            parent::formConfig(),
            ['isSingle' => true],
        );
    }

    final public function find($id): array
    {
        return $this->findSingle();
    }

    final public function update($id, array $data)
    {
        return $this->updateSingle($data);
    }

    final public function storeInstance($data)
    {
        throw new SharpException('Store is not possible in a SingleSharpForm.');
    }

    /** @deprecated will be removed in v9. */
    final public function delete($id): void
    {
        throw new SharpException('Delete is not possible in a SingleSharpForm.');
    }

    abstract protected function findSingle();

    abstract protected function updateSingle(array $data);
}
