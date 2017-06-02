<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TechnicalReview extends Model
{
    protected $dates = [
        "created_at", "updated_at", "starts_at"
    ];

    public function spaceship()
    {
        return $this->belongsTo(Spaceship::class);
    }
}
