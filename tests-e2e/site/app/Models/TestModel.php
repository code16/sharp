<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Translatable\HasTranslations;

class TestModel extends Model
{
    /** @use HasFactory<\Database\Factories\TestModelFactory> */
    use HasFactory;

    use HasTranslations;

    protected $guarded = [];
    public array $translatable = [
        'text_localized',
        'textarea_localized',
        'editor_html_localized',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'date_time' => 'datetime',
            'list' => 'array',
            'autocomplete_list' => 'array',
            'select_dropdown_multiple' => 'array',
            'select_checkboxes' => 'array',
        ];
    }

    public function upload(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')
            ->where('model_key', 'upload');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(TestTag::class);
    }

    public function getDefaultAttributesFor($attribute)
    {
        return in_array($attribute, ['upload'])
            ? ['model_key' => $attribute]
            : [];
    }
}
