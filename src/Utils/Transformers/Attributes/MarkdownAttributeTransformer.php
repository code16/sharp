<?php

namespace Code16\Sharp\Utils\Transformers\Attributes;

use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;

class MarkdownAttributeTransformer implements SharpAttributeTransformer
{
    protected bool $nl2br = false;

    public function setNewLineOnCarriageReturn(bool $nb2br = true): self
    {
        $this->nl2br = $nb2br;

        return $this;
    }

    public function apply($value, $instance = null, $attribute = null)
    {
        if (! $instance->$attribute) {
            return null;
        }

        $environment = new Environment([
            'html_input' => 'allow',
            'renderer' => [
                'soft_break' => $this->nl2br ? '<br>' : "\n",
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());

        $converter = new MarkdownConverter($environment);

        return $converter->convertToHtml($instance->$attribute)->getContent();
    }
}
