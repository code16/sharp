<?php

namespace Code16\Sharp\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Person extends Model
{
    protected $guarded = [];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
