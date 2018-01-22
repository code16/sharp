<?php

namespace App\Sharp;

class PilotSeniorSharpValidator extends PilotJuniorSharpValidator
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return parent::rules() + [
            'xp' => 'required|integer',
        ];
    }
}