<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use DOMDocument;
use DOMElement;
use DOMNode;
use Masterminds\HTML5;

trait HandlesHtmlContent
{
    protected function parseHtml(string $html): DOMDocument
    {
        libxml_use_internal_errors(true);

        return (new HTML5())->loadHTML("<body>$html</body>");
    }

    protected function toHtml(DOMDocument $domDocument): string
    {
        return (new HTML5())->saveHTML($this->getAllRootNodes($domDocument));
    }

    protected function getInnerHtml(DOMElement $element): string
    {
        $html = new HTML5();

        return collect($element->childNodes)
            ->map(fn (DOMNode $node) => $html->saveHTML($node))
            ->implode('');
    }

    protected function setInnerHtml(DOMElement $element, string $html): void
    {
        $fragment = (new HTML5())->loadHTMLFragment($html, ['target_document' => $element->ownerDocument]);
        while ($element->hasChildNodes()) {
            $element->removeChild($element->firstChild);
        }
        if ($fragment->hasChildNodes()) {
            $element->appendChild($fragment);
        }
    }

    protected function getAllRootNodes(DOMDocument $domDocument): \DOMNodeList
    {
        return $domDocument->getElementsByTagName('body')[0]->childNodes;
    }

    /**
     * @return DOMElement[]
     */
    protected function getRootElementsByTagNames(DOMDocument $domDocument, ?array $tagNames): array
    {
        return collect($this->getAllRootNodes($domDocument))
            ->filter(fn ($node) => $node instanceof DOMElement && in_array($node->tagName, $tagNames))
            ->toArray();
    }
}
