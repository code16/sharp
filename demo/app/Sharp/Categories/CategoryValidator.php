<?php

namespace App\Sharp\Categories;

use Illuminate\Foundation\Http\FormRequest;

class CategoryValidator extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
        ];
    }
}
