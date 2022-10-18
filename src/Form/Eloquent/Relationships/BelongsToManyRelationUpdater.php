<?php

namespace Code16\Sharp\Form\Eloquent\Relationships;

class BelongsToManyRelationUpdater
{
    public function update(object $instance, string $attribute, array $value): void
    {
        $collection = collect($value);
        $keyName = explode('.', $instance->$attribute()->getRelated()->getQualifiedKeyName())[1];

        // First sync all existing related items (ie: those with an id).
        // Can be from SharpFormTagsField or SharpFormSelectField "multiple"
        $instance->$attribute()->sync(
            $collection
                ->filter(fn ($item) => ! is_null($item[$keyName]))
                ->pluck($keyName)
                ->all(),
        );

        // Then create all non-existing related items (only for SharpFormTagsField "creatable" mode)
        $collection
            ->filter(fn ($item) => is_null($item[$keyName]))
            ->each(function ($item) use ($instance, $attribute, $keyName) {
                unset($item[$keyName]);
                $instance->$attribute()->create($item);
            });
    }
}
