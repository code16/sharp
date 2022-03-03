<?php

namespace App\Sharp\Posts\Blocks;

use Illuminate\Foundation\Http\FormRequest;

class PostBlockVideoValidator extends FormRequest
{
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:1000'],
        ];
    }
}
