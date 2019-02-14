<?php

namespace App\Sharp;

use Code16\Sharp\Http\WithSharpContext;
use Illuminate\Foundation\Http\FormRequest;

class PilotJuniorSharpValidator extends FormRequest
{
    use WithSharpContext;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }
}