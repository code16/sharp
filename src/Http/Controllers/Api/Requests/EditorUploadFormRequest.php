<?php

namespace Code16\Sharp\Http\Controllers\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditorUploadFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => 'required',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge($this->get('data'));
    }
}
