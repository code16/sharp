<?php

namespace App\Sharp;

class PilotSeniorSharpValidator extends PilotJuniorSharpValidator
{
    public function rules()
    {
        return parent::rules() + [
            'xp' => 'required|integer',
        ];
    }
}