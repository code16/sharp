<?php

namespace App\Sharp\Posts\Blocks;

class PostBlockTextForm extends AbstractPostBlockForm
{
    protected static string $postBlockType = "text";
    protected ?string $formValidatorClass = PostBlockTextValidator::class;
}
