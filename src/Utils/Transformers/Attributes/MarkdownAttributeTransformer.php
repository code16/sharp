<?php

namespace Code16\Sharp\Utils\Transformers\Attributes;

use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;
use League\CommonMark\GithubFlavoredMarkdownConverter;

class MarkdownAttributeTransformer implements SharpAttributeTransformer
{
    protected bool $nl2br = false;

    public function setNewLineOnCarriageReturn(bool $nb2br = true): self
    {
        $this->nl2br = $nb2br;
        
        return $this;
    }

    function apply($value, $instance = null, $attribute = null)
    {
        if(!$instance->$attribute) {
            return null;
        }
        
        $converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'allow',
            'renderer' => [
                'soft_break' => $this->nl2br ? "<br>" : "\n",
            ],
        ]);
        
        return $converter->convertToHtml($instance->$attribute)->getContent();
    }
}