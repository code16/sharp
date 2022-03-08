<?php

namespace App\Sharp\Posts;

use Illuminate\Foundation\Http\FormRequest;

class PostValidator extends FormRequest
{
    public function rules(): array
    {
        return [
            'title.fr' => ['required', 'string', 'max:150'],
            'title.en' => ['required', 'string', 'max:150'],
            'content.text.fr' => ['required', 'string', 'max:2000'],
            'content.text.en' => ['required', 'string', 'max:2000'],
            'published_at' => ['required', 'date'],
            'attachments.*.title' => ['required', 'string', 'max:50'],
            'attachments.*.link_url' => ['required_if:attachments.*.is_link,true,1', 'nullable', 'url', 'max:150'],
            'attachments.*.document' => ['required_if:attachments.*.is_link,false,0'],
        ];
    }
}
