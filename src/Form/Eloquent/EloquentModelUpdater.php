<?php

namespace Code16\Sharp\Form\Eloquent;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;

class EloquentModelUpdater
{
    protected array $relationshipsConfiguration = [];
    protected array $relationships = [];
    protected ?Closure $fillAfterUpdateUsing = null;

    public function update(Model $instance, array $data, bool $isPreview = false): Model
    {
        $this->fillInstance($instance, $data);

        if ($isPreview) {
            return $instance;
        }

        // End of "normal" attributes.
        $instance->save();

        if ($closure = $this->fillAfterUpdateUsing) {
            $data = $closure($instance->getKey());

            // Second pass, to handle attributes that could depend on the instance id.
            $this->fillInstance($instance, $data);

            if ($instance->isDirty()) {
                $instance->save();
            }
        }

        // Next, handle relationships.
        $this->saveRelationships($instance);

        return $instance;
    }

    public function fillAfterUpdateUsing(Closure $closure): self
    {
        $this->fillAfterUpdateUsing = $closure;

        return $this;
    }

    protected function fillInstance(Model $instance, array $data): void
    {
        foreach ($data as $attribute => $value) {
            if ($this->isRelationship($instance, $attribute)) {
                $this->relationships[$attribute] = $value;

                continue;
            }

            $this->valuateAttribute($instance, $attribute, $value);
        }
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
        if (str($attribute)->contains(':')) {
            return true;
        }

        if ($instance->isRelation($attribute)) {
            if ($instance->relationResolver(get_class($instance), $attribute)) {
                // Custom relation resolver
                return true;
            }

            // Check return type to (try to) ignore non relation methods by their return type
            $returnType = (new ReflectionMethod($instance, $attribute))->getReturnType();

            return $returnType === null
                || ($returnType instanceof ReflectionNamedType && is_subclass_of($returnType->getName(), Relation::class));
        }

        return false;
    }

    protected function saveRelationships(Model $instance): void
    {
        foreach ($this->relationships as $attribute => $value) {
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
