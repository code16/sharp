<?php

namespace App\Sharp;

use Illuminate\Foundation\Http\FormRequest;

class PilotJuniorSharpValidator extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }
}
