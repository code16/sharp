<?php

namespace App\Sharp\Posts\Blocks;

use Illuminate\Foundation\Http\FormRequest;

class PostBlockTextValidator extends FormRequest
{
    public function rules(): array
    {
        return [
            'content.text' => ['required', 'string', 'max:1000'],
        ];
    }
}
