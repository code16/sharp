<?php

namespace App\Sharp;

use Illuminate\Foundation\Http\FormRequest;

class SpaceshipSharpValidator extends FormRequest
{
    public function rules()
    {
        return [
            'name.fr' => 'required',
            'type_id' => 'required',
            'pictures.*.file' => 'required',
            'capacity' => ['integer', 'min:10', 'required'],
            'picture' => 'required_with:picture:legend',
        ];
    }
}
