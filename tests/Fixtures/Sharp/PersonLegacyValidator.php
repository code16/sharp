<?php

namespace Code16\Sharp\Tests\Fixtures\Sharp;

use Illuminate\Foundation\Http\FormRequest;

class PersonLegacyValidator extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
        ];
    }
}