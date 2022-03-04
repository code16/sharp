<?php

namespace App\Sharp\Profile;

use Code16\Sharp\Form\Validator\SharpFormRequest;

class MyProfileValidator extends SharpFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:300'],
        ];
    }
}
