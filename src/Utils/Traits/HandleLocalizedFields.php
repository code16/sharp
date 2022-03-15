<?php

namespace Code16\Sharp\Utils\Traits;

trait HandleLocalizedFields
{
    final public function hasDataLocalizations(): bool
    {
        foreach ($this->fields() as $field) {
            if ($field['localized'] ?? false) {
                return true;
            }

            if ($field['type'] === 'list') {
                foreach ($field['itemFields'] as $itemField) {
                    if ($itemField['localized'] ?? false) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function getDataLocalizations(): array
    {
        return [];
    }
}
