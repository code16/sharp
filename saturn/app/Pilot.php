<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pilot extends Model
{
    protected $guarded = [];

    public function spaceships(): BelongsToMany
    {
        return $this->belongsToMany(Spaceship::class);
    }
}
