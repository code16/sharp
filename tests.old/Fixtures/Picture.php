<?php

namespace Code16\Sharp\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    protected $guarded = [];

    public function picturable()
    {
        return $this->morphTo();
    }
}
