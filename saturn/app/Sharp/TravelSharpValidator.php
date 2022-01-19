<?php

namespace App\Sharp;

use Code16\Sharp\Form\Validator\SharpFormRequest;

class TravelSharpValidator extends SharpFormRequest
{
    public function rules()
    {
        return [
            'destination' => 'required',
            'description' => 'required|min:20',
        ];
    }
}
