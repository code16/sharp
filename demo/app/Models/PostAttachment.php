<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PostAttachment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function document(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')
            ->where('model_key', 'document');
    }

    public function getDefaultAttributesFor($attribute)
    {
        return in_array($attribute, ['document'])
            ? ['model_key' => $attribute]
            : [];
    }
}
