<?php

namespace Code16\Sharp\Form\Eloquent\Relationships;

class BelongsToManyRelationUpdater
{
    /**
     * @var array
     */
    protected $handledIds = [];

    /**
     * @param $instance
     * @param string $attribute
     * @param array $value
     */
    public function update($instance, $attribute, $value)
    {
        $collection = collect($value);

        // First sync all existing related items (ie: those with an id)
        $instance->$attribute()->sync(
            $collection->filter(function($item) {
                return !is_null($item["id"]);
            })->pluck("id")->all()
        );

        // Then create all non-existing related items
        $collection->filter(function($item) {
            return is_null($item["id"]);
        })->each(function($item) use($instance, $attribute) {
            unset($item["id"]);
            $instance->$attribute()->create($item);
        });
    }

}