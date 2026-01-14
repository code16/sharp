<?php

namespace Code16\Sharp\Utils\Testing\Form;

use Code16\Sharp\Form\SharpForm;

trait FormatsDataForUpdate
{
    protected function formatDataForUpdate(SharpForm $form, array $data, ?array $baseData = null): array
    {
        return [
            ...$baseData ?: [],
            ...collect($form->applyFormatters($data))
                ->when($baseData)->only(array_keys($data))
                ->all(),
        ];
    }
}
