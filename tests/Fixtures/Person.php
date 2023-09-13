<?php

namespace Code16\Sharp\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Person extends Model
{
    protected $guarded = [];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function collaborators(): HasMany
    {
        return $this->hasMany(Person::class, 'chief_id');
    }

    public function director(): HasOne
    {
        return $this->hasOne(Person::class, 'chief_id')
            ->orderBy('id');
    }

    public function colleagues(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'colleagues', 'person1_id', 'person2_id');
    }

    public function photo(): MorphOne
    {
        return $this->morphOne(Picture::class, 'picturable');
    }

    public function pictures(): MorphMany
    {
        return $this->morphMany(Picture::class, 'picturable');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
