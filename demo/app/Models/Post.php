<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public static function scopeOnline(Builder $builder): void
    {
        $builder->where('state', 'online');
    }

    public static function scopePublishedSince(Builder $builder, Carbon $since): void
    {
        $builder->where('published_at', '>=', $since);
    }

    public static function scopeByAuthor(Builder $builder, User $author): void
    {
        $builder->where('author_id', $author->id);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cover(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')
            ->where('model_key', 'cover');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PostAttachment::class)
            ->orderBy('order');
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(PostBlock::class)
            ->orderBy('order');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function isOnline(): bool
    {
        return $this->state === 'online';
    }

    public function getDefaultAttributesFor($attribute)
    {
        return in_array($attribute, ['cover'])
            ? ['model_key' => $attribute]
            : [];
    }
}
