<?php

namespace App\Sharp;

use Code16\Sharp\Form\Validator\SharpFormRequest;

class TravelSharpValidator extends SharpFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'destination' => 'required',
            'description' => 'required|min:20',
        ];
    }
}