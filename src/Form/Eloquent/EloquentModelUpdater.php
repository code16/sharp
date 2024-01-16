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
        return str_contains($attribute, ':') || $instance->isRelation($attribute);
    }

    protected function saveRelationships(Model $instance, array $relationships): void
    {
        foreach ($relationships as $attribute => $value) {
            $relAttribute = explode(':', $attribute)[0];
            $type = get_class($instance->{$relAttribute}());

            $relationshipUpdater = app('Code16\Sharp\Form\Eloquent\Relationships\\'
                .(new ReflectionClass($type))->getShortName()
                .'RelationUpdater');

            // Special code to handle file_name attribute in file upload case, when there is an {id} parameter
            if (is_array($value) && isset($value['file_name']) && str($value['file_name'])->contains('{id}')) {
                $value['file_name'] = str($value['file_name'])
                    ->replace('{id}', $instance->id);
            }

            $relationshipUpdater->update(
                $instance, $attribute, $value, $this->relationshipsConfiguration[$attribute] ?? null,
            );
        }
    }
}
