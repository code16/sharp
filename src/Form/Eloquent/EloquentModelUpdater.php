<?php

namespace Code16\Sharp\Form\Eloquent;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use ReflectionClass;

class EloquentModelUpdater
{
    protected array $relationshipsConfiguration = [];

    public function update(Model $instance, array $data): Model
    {
        $relationships = [];

        foreach ($data as $attribute => $value) {
            if ($this->isRelationship($instance, $attribute)) {
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

    public function initRelationshipsConfiguration(array|Arrayable $configuration): self
    {
        $this->relationshipsConfiguration = is_array($configuration)
            ? $configuration
            : $configuration->toArray();

        return $this;
    }

    protected function valuateAttribute(Model $instance, string $attribute, $value): void
    {
        $instance->setAttribute($attribute, $value);
    }

    protected function isRelationship(Model $instance, string $attribute): bool
    {
        if (str_contains($attribute, ':')) {
            return true;
        }

        if (method_exists($instance, $attribute)) {
            $reflection = new ReflectionClass($instance->$attribute());

            return $reflection && $reflection->isSubclassOf(Relation::class);
        }

        return false;
    }

    protected function saveRelationships(Model $instance, array $relationships): void
    {
        foreach ($relationships as $attribute => $value) {
            $relAttribute = explode(':', $attribute)[0];
            $type = get_class($instance->{$relAttribute}());

            $relationshipUpdater = app('Code16\Sharp\Form\Eloquent\Relationships\\'
                .(new ReflectionClass($type))->getShortName()
                .'RelationUpdater');

            $relationshipUpdater->update(
                $instance, $attribute, $value, $this->relationshipsConfiguration[$attribute] ?? null,
            );
        }
    }
}
