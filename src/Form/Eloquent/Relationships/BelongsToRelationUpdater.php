<?php

namespace Code16\Sharp\Form\Eloquent\Relationships;

use Code16\Sharp\Form\Eloquent\Relationships\Utils\CanCreateRelatedModel;

class BelongsToRelationUpdater
{
    use CanCreateRelatedModel;
    
    public function update(object $instance, string $attribute, $value)
    {
        if(strpos($attribute, ":") !== false) {
            // This is a relation attribute update case (eg: mother:name)
            list($attribute, $subAttribute) = explode(":", $attribute);

            if($instance->$attribute) {
                $instance->$attribute()->update([
                    $subAttribute => $value
                ]);

                return;
            }

            // Creation case
            if(!$value) {
                return;
            }

            $value = $this->createRelatedModel($instance, $attribute, [$subAttribute => $value]);
        }

        $instance->$attribute()->associate($value);
        $instance->save();
    }
}