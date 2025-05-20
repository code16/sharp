<?php

namespace App\Sharp\Posts\Blocks;

use Illuminate\Foundation\Http\FormRequest;

class PostBlockVideoValidator extends FormRequest
{
    public function rules(): array
    {
        return [
            'content' => [
                'required',
                'string',
                'regex:/^<iframe.*?src="https?:\/\/(www\.)?youtube\.com\/embed\/[a-zA-Z0-9_-]+(\?.*?)?".*?><\/iframe>$/s',
                'max:1000',
            ],
        ];
    }
}
