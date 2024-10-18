<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Relationships\Fixtures;

use Code16\Sharp\Tests\Fixtures\Person;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PersonWithFixedId extends Person
{
    protected $table = 'people';
    public $incrementing = false;

    public function collaborators(): HasMany
    {
        return $this->hasMany(PersonWithFixedId::class, 'chief_id');
    }
}
