<?php

namespace App\Sharp;

use Code16\Sharp\Http\WithSharpFormContext;
use Illuminate\Foundation\Http\FormRequest;

class SpaceshipSharpValidator extends FormRequest
{
    use WithSharpFormContext;

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
            'name.fr' => 'required',
            'type_id' => 'required',
//            'picture' => 'required',
            'pictures.*.file' => 'required',
//            'pictures.*.legend' => 'required',
            "capacity" => "integer|min:10|required"
        ];
    }
}