<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $guarded = [];

    const TYPES = [
        'tech' => 'Technical',
        'ser'  => 'Services',
        'oth'  => 'Other',
    ];

    const SUBTYPES = [
        'tech' => [
            'eng'  => 'Fast engine',
            'eng+' => 'Super Fast© engine',
            'eco'  => 'Eco mode',
        ],
        'ser' => [
            'bar'  => 'Bar aboard',
            'golf' => 'Golf course',
        ],
        'oth' => [
            'ac'  => 'Air conditioning',
            'col' => 'Rainbow colors',
        ],
    ];
}
