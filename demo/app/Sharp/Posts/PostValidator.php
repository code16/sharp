<?php

namespace App\Sharp\Posts;

use Illuminate\Foundation\Http\FormRequest;

class PostValidator extends FormRequest
{
    public function rules(): array
    {
        return [
            'title.fr' => ['required', 'string', 'max:150'],
            'title.en' => ['required', 'string', 'max:150'],
            'content.text.fr' => ['required', 'string', 'max:1000'],
            'content.text.en' => ['required', 'string', 'max:1000'],
            'published_at' => ['required', 'date'],
        ];
    }
}