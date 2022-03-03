<?php

namespace App\Sharp\Posts\Blocks;

class PostBlockVideoForm extends AbstractPostBlockForm
{
    protected static string $postBlockType = 'video';
    protected static string $postBlockHelpText = 'Please provide a valid Youtube embed text';
    protected ?string $formValidatorClass = PostBlockVideoValidator::class;
}
