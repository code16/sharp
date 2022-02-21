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
            //            'picture' => 'required',
            'pictures.*.file' => 'required',
            //            'pictures.*.legend' => 'required',
            'capacity' => 'integer|min:10|required',
        ];
    }
}
