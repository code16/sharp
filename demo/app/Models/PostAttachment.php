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
    protected $casts = [
        'is_link' => 'boolean',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function document(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')
            ->withAttributes(['model_key' => 'document']);
    }
}
