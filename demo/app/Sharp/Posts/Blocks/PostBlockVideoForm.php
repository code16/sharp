<?php

namespace App\Sharp\Posts\Blocks;

class PostBlockVideoForm extends AbstractPostBlockForm
{
    protected static string $postBlockType = 'video';
    protected static string $postBlockHelpText = 'Please provide a valid Youtube embed text';

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
