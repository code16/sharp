<?php

namespace Code16\Sharp\Form\Eloquent;

use Illuminate\Database\Eloquent\Model;

class EloquentModelUpdater
{
    /** @var array */
    protected $relationshipsConfiguration;

    /**
     * @param Model $instance
     * @param array $data
     * @return Model
     * @throws \ReflectionException
     */
    function update($instance, array $data)
    {
        $relationships = [];

        foreach($data as $attribute => $value) {
            if($this->isRelationship($instance, $attribute)) {
                $relationships[$attribute] = $value;

                continue;
            }

            $this->valuateAttribute($instance, $attribute, $value);
        }

        // End of "normal" attributes.
        $instance->save();

        // Next, handle relationships.
        $this->saveRelationships($instance, $relationships);

        return $instance;
    }

    /**
     * @param array $configuration
     * @return $this
     */
    function initRelationshipsConfiguration($configuration)
    {
        $this->relationshipsConfiguration = $configuration;

        return $this;
    }

    /**
     * Valuates the $attribute with $value
     *
     * @param Model $instance
     * @param string $attribute
     * @param $value
     */
    protected function valuateAttribute($instance, string $attribute, $value)
    {
        $instance->setAttribute($attribute, $value);
    }

    /**
     * @param Model $instance
     * @param string $attribute
     * @return bool
     */
    protected function isRelationship($instance, $attribute): bool
    {
        return strpos($attribute, ":") !== false || method_exists($instance, $attribute);
    }

    /**
     * @param Model $instance
     * @param array $relationships
     * @throws \ReflectionException
     */
    protected function saveRelationships($instance, array $relationships)
    {
        foreach ($relationships as $attribute => $value) {
            $relAttribute = explode(":", $attribute)[0];
            $type = get_class($instance->{$relAttribute}());

            $relationshipUpdater = app('Code16\Sharp\Form\Eloquent\Relationships\\'
                . (new \ReflectionClass($type))->getShortName()
                . 'RelationUpdater');

            $relationshipUpdater->update(
                $instance, $attribute, $value, $this->relationshipsConfiguration[$attribute] ?? null
            );
        }
    }
}