<?php

namespace App\Sharp;

use Illuminate\Foundation\Http\FormRequest;

class FeatureSharpValidator extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }
}
