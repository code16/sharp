<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PostBlock extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function files(): MorphMany
    {
        return $this->morphMany(Media::class, 'model')
            ->where('model_key', 'files');
    }

    public function getDefaultAttributesFor($attribute)
    {
        return in_array($attribute, ['files'])
            ? ['model_key' => $attribute]
            : [];
    }
}
