<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Travel extends Model
{
    protected $dates = ["created_at", "updated_at", "departure_date"];

    public function spaceship()
    {
        return $this->belongsTo(Spaceship::class);
    }

    public function delegates()
    {
        return $this->belongsToMany(Passenger::class, 'travel_delegates');
    }
}
