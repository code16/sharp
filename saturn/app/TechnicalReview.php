<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class TechnicalReview extends Model
{
    use HasTranslations;

    public $translatable = ['report'];

    protected $dates = [
        "created_at", "updated_at", "starts_at"
    ];

    public function spaceship()
    {
        return $this->belongsTo(Spaceship::class);
    }
}
