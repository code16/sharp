<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Masterminds\HTML5;

trait HandlesHtmlContent
{
    protected function parseHtml(string $html): \DOMDocument
    {
        libxml_use_internal_errors(true);

        return (new HTML5())->loadHTML("<body>$html</body>");
    }

    protected function getHtml(\DOMDocument $domDocument): string
    {
        return (new HTML5())->saveHTML($this->getAllRootNodes($domDocument));
    }

    protected function getAllRootNodes(\DOMDocument $domDocument): \DOMNodeList
    {
        return $domDocument->getElementsByTagName('body')[0]->childNodes;
    }

    protected function getRootElementsByTagNames(\DOMDocument $domDocument, ?array $tagNames): array
    {
        return collect($this->getAllRootNodes($domDocument))
            ->filter(fn ($node) => $node instanceof \DOMElement && in_array($node->tagName, $tagNames))
            ->toArray();
    }
}
