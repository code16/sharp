<?php

namespace Code16\Sharp\Utils\Sanitization;

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use DOMElement;
use Masterminds\HTML5;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

trait FormatsSanitizedValue
{
    private ?HtmlSanitizer $sanitizer = null;

    private function sanitizeHtmlIfNeeded(
        IsSharpFieldWithHtmlSanitization $field,
        ?string $value
    ): ?string {
        if (! $value || ! str_contains($value, '<') || ! $field->isSanitizingHtml()) {
            return $value;
        }

        if ($field instanceof SharpFormEditorField) {
            $encoded = $this->encodeEmbedsAndRawHtml($field, $value);
            $sanitized = $this->sanitizer()->sanitize($encoded);

            return $this->decodeEmbedsAndRawHtml($field, $sanitized);
        }

        return $this->sanitizer()->sanitize($value);
    }

    private function sanitizer(): HtmlSanitizer
    {
        $config = (new HtmlSanitizerConfig())
            ->allowSafeElements()
            ->allowElement('iframe')
            ->allowRelativeLinks()
            ->allowRelativeMedias()
            ->allowElement('div', ['data-encoded-content'])
            ->allowAttribute('class', allowedElements: '*')
            ->allowAttribute('style', allowedElements: '*')
            ->withMaxInputLength(500000);

        return $this->sanitizer ??= new HtmlSanitizer($config);
    }

    private function isEncodingNeeded(SharpFormEditorField $field): bool
    {
        return count($field->embeds())
            || $field->uploadsConfig()
            || in_array(SharpFormEditorField::RAW_HTML, $field->getToolbar());
    }

    private function encodeEmbedsAndRawHtml(SharpFormEditorField $field, string $value): string
    {
        if (! $this->isEncodingNeeded($field)) {
            return $value;
        }

        $fragment = (new HTML5())->loadHTMLFragment($value);
        $embedTags = $field->embeds()->map(fn (SharpFormEditorEmbed $embed) => $embed->tagName())->all();

        for ($i = 0; $i < $fragment->childNodes->length; $i++) {
            $node = $fragment->childNodes->item($i);
            if ($node instanceof DOMElement
                && (in_array($node->tagName, $embedTags)
                    || str_starts_with($node->tagName, 'x-')
                    || $node->hasAttribute('data-html-content'))
            ) {
                $replacement = $node->ownerDocument->createElement('div');
                $replacement->setAttribute(
                    'data-encoded-content',
                    htmlspecialchars((new HTML5())->saveHTML($node), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
                );
                $node->parentNode->replaceChild($replacement, $node);
            }
        }

        return (new HTML5())->saveHTML($fragment->childNodes);
    }

    private function decodeEmbedsAndRawHtml(SharpFormEditorField $field, string $value): string
    {
        if (! $this->isEncodingNeeded($field)) {
            return $value;
        }

        $fragment = (new HTML5())->loadHTMLFragment($value);

        for ($i = 0; $i < $fragment->childNodes->length; $i++) {
            $node = $fragment->childNodes->item($i);
            if ($node instanceof DOMElement && $node->hasAttribute('data-encoded-content')) {
                $replacement = (new HTML5())->loadHTMLFragment(
                    htmlspecialchars_decode($node->getAttribute('data-encoded-content')),
                    ['target_document' => $node->ownerDocument]
                )->childNodes->item(0);

                $node->parentNode->replaceChild($replacement, $node);
            }
        }

        return (new HTML5())->saveHTML($fragment->childNodes);
    }
}
