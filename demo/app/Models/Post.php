<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Translatable\HasTranslations;

class Post extends Model
{
    use HasFactory, HasTranslations;
    
    public array $translatable = [
        'title',
        'content',
    ];
    protected $guarded = [];
    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cover(): MorphOne
    {
        return $this->morphOne(Media::class, "model")
            ->where("model_key", "cover");
    }

    public function isOnline(): bool
    {
        return $this->state === 'online';
    }

    public function getDefaultAttributesFor($attribute)
    {
        return in_array($attribute, ["cover"])
            ? ["model_key" => $attribute]
            : [];
    }
}
