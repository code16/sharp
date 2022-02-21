<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpaceshipType extends Model
{
    protected $casts = [
        'brands' => 'array',
    ];
}
