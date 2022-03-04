<?php

namespace App\Sharp\MyProfile;

use Code16\Sharp\Form\Validator\SharpFormRequest;

class MyProfileValidator extends SharpFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:300',],
        ];
    }
}
