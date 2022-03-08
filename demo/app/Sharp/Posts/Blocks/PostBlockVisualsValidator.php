<?php

namespace App\Sharp\Posts\Blocks;

use Illuminate\Foundation\Http\FormRequest;

class PostBlockVisualsValidator extends FormRequest
{
    public function rules(): array
    {
        return [
            'files' => ['required', 'array'],
            'files.*.file' => ['required'],
        ];
    }
}
