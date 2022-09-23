<?php

namespace App\Sharp\TestForm;

use Illuminate\Foundation\Http\FormRequest;

class TestValidator extends FormRequest
{
    public function rules(): array
    {
        return [
            'date' => 'required|before_or_equal:'.date('Y-m-d'),
        ];
    }
}
