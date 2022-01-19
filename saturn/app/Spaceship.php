<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Translatable\HasTranslations;

class Spaceship extends Model
{
    use HasTranslations;

    public $translatable = ['name'];

    protected $guarded = [];

    public function type(): BelongsTo
    {
        return $this->belongsTo(SpaceshipType::class);
    }

    public function corporation(): BelongsTo
    {
        return $this->belongsTo(Corporation::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(TechnicalReview::class)
            ->orderBy('starts_at');
    }

    public function pilots(): BelongsToMany
    {
        return $this->belongsToMany(Pilot::class);
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class);
    }

    public function manual(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')
            ->where('model_key', 'manual');
    }

    public function picture(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')
            ->where('model_key', 'picture');
    }

    public function pictures(): MorphMany
    {
        return $this->morphMany(Media::class, 'model')
            ->where('model_key', 'pictures')
            ->orderBy('order');
    }

    public function getDefaultAttributesFor(string $attribute): array
    {
        return in_array($attribute, ['manual', 'picture', 'pictures'])
            ? ['model_key' => $attribute]
            : [];
    }
}
