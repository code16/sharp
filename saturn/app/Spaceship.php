<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Spaceship extends Model
{

    public function type()
    {
        return $this->belongsTo(SpaceshipType::class);
    }

    public function reviews()
    {
        return $this->hasMany(TechnicalReview::class)
            ->orderBy("starts_at");
    }

    public function pilots()
    {
        return $this->belongsToMany(Pilot::class);
    }

    public function picture()
    {
        return $this->morphOne(Media::class, "model")
            ->where("model_key", "picture");
    }

    public function pictures()
    {
        return $this->morphMany(Media::class, "model")
            ->where("model_key", "pictures")
            ->orderBy("order");
    }

    public function getDefaultAttributesFor($attribute)
    {
        return in_array($attribute, ["picture", "pictures"])
            ? ["model_key" => $attribute]
            : [];
    }
}