<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Spaceship extends Model
{
    use HasTranslations;

    public $translatable = ['name'];

    protected $guarded = [];

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

    public function features()
    {
        return $this->belongsToMany(Feature::class);
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

//    public function descriptionPictures()
//    {
//        return $this->morphMany(Media::class, "model")
//            ->where("model_key", "markdown_description");
//    }

    public function getDefaultAttributesFor($attribute)
    {
        return in_array($attribute, ["picture", "pictures"])
            ? ["model_key" => $attribute]
            : [];
    }
}