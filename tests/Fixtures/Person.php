<?php

namespace Code16\Sharp\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $guarded = [];

    public function mother()
    {
        return $this->belongsTo(Person::class);
    }

    public function elderSon()
    {
        return $this->hasOne(Person::class, "mother_id");
    }

    public function sons()
    {
        return $this->hasMany(Person::class, "mother_id");
    }

    public function friends()
    {
        return $this->belongsToMany(Person::class, "friends", "person1_id", "person2_id");
    }

    public function picture()
    {
        return $this->morphOne(Picture::class, "picturable");
    }

    public function pictures()
    {
        return $this->morphMany(Picture::class, "picturable");
    }
}