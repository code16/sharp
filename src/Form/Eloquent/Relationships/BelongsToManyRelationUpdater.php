<?php

namespace Code16\Sharp\Form\Eloquent\Relationships;

class BelongsToManyRelationUpdater
{
    public function update(object $instance, string $attribute, array $value, ?array $sortConfiguration = null): void
    {
        $collection = collect($value);
        $keyName = explode('.', $instance->$attribute()->getRelated()->getQualifiedKeyName())[1];

        $instance->$attribute()->sync(
            $collection
                ->map(function ($item) use ($instance, $attribute, $keyName) {
                    if (!is_null($item[$keyName])) {
                        return $item;
                    }

                    // Create all non-existing related items (only for SharpFormTagsField "creatable" mode)
                    return $instance
                        ->$attribute()
                        ->create($item);
                })
                ->when(
                    $sortConfiguration,
                    fn ($collection) => $collection
                        ->mapWithKeys(fn ($item, $k) => [
                            $item[$keyName] => [$sortConfiguration['orderAttribute'] => ($k + 1)]
                        ]),
                    fn ($collection) => $collection->pluck($keyName)
                )
                ->all(),
        );
    }
}
