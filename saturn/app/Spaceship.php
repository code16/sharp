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

    public function getDefaultAttributesFor($attribute)
    {
        return $attribute == "picture"
            ? ["model_key" => "picture"]
            : [];
    }
}