<?php

namespace Code16\Sharp\Tests\Fixtures;

use Illuminate\Foundation\Http\FormRequest;

class PersonSharpValidator extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return ['name' => 'required'];
    }
}