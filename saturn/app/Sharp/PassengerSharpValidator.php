<?php

namespace App\Sharp;

use Illuminate\Foundation\Http\FormRequest;

class PassengerSharpValidator extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }
}