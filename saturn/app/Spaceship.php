<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Spaceship extends Model
{

    public function type()
    {
        return $this->belongsTo(SpaceshipType::class);
    }
}