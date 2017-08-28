<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Relationships;

trait TestWithSharpList
{
    protected function formatData(array $data)
    {
        $itemsData = [];

        foreach($data as $item) {
            $itemData = new UpdateRequestData;

            foreach ($item as $attribute => $value) {
                $itemData->add($attribute)
                    ->setValue($value["value"])
                    ->setValuator($value["valuator"])
                    ->setFormField($value["field"]);
            }

            $itemsData[] = $itemData;
        }

        return $itemsData;
    }
}