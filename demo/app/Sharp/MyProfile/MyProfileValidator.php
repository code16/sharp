<?php

namespace App\Sharp\MyProfile;

use App\Models\Enums\UserRole;
use Code16\Sharp\Form\Validator\SharpFormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class MyProfileValidator extends SharpFormRequest
{
    public function rules(): array
    {
        return [
            "name" => [
                "required",
                "max:300"
            ],
        ];
    }
}


