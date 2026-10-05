<?php

namespace Code16\Sharp\Utils\Sanitization;

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Str;
use Masterminds\HTML5;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

trait FormatsSanitizedValue
{
    private function sanitizeHtmlIfNeeded(
        IsSharpFieldWithHtmlSanitization $field,
        ?string $value
    ): ?string {
        if (! $value || ! str_contains($value, '<') || ! $field->isSanitizingHtml()) {
            return $value;
        }

        if ($field instanceof SharpFormEditorField) {
            $embeds = $this->collectEmbeds($field);

            if (in_array(SharpFormEditorField::RAW_HTML, $field->getToolbar())
                && str_contains($value, 'data-html-content')
            ) {
                // Security risk (opt-in): RAW_HTML content bypasses sanitization, as warned in docs/guide/form-fields/editor.md
                return $this->decodeRawHtml(
                    $this->sanitizer($embeds, allowEncodedRawHtml: true)->sanitize($this->encodeRawHtml($value))
                );
            }

            return $this->sanitizer($embeds)->sanitize($value);
        }

        return $this->sanitizer()->sanitize($value);
    }

    /**
     * Embeds allowed by the field and, recursively, by their editor "slot" fields.
     *
     * @return array<string, SharpFormEditorEmbed>
     */
    private function collectEmbeds(SharpFormEditorField $field, array $collected = []): array
    {
        foreach ($field->embeds() as $key => $embed) {
            if (isset($collected[$key])) {
                continue;
            }

            $collected[$key] = $embed;

            if (($slot = $embed->getBuiltFields()->get('slot')) instanceof SharpFormEditorField) {
                $collected = $this->collectEmbeds($slot, $collected);
            }
        }

        return $collected;
    }

    /**
     * @param  SharpFormEditorEmbed[]  $embeds
     */
    private function sanitizer(array $embeds = [], bool $allowEncodedRawHtml = false): HtmlSanitizer
    {
        $config = (new HtmlSanitizerConfig())
            ->allowSafeElements()
            ->allowAttribute('data-id', ['a', 'li'])
            ->allowElement('iframe', [
                'allow',
                'allowfullscreen',
                'loading',
                'name',
                'referrerpolicy',
                'sandbox',
                'src',
                'width',
                'height',
                'id',
                'title',
                'aria-label',
                'frameborder',
                'marginwidth',
                'marginheight',
                'scrolling',
            ])
            ->allowRelativeLinks()
            ->allowRelativeMedias()
            ->allowElement('div', $allowEncodedRawHtml ? ['data-encoded-content', 'data-id'] : ['data-id'])
            ->allowElement('x-sharp-image', ['data-key', 'file', 'legend'])
            ->allowElement('x-sharp-file', ['data-key', 'file', 'legend'])
            ->allowAttribute('class', allowedElements: '*')
            ->allowAttribute('style', allowedElements: '*')
            ->withMaxInputLength(500000);

        foreach ($embeds as $embed) {
            $config = $config->allowElement($embed->tagName(), [
                'data-key',
                ...$embed->getBuiltFields()
                    ->keys()
                    ->reject(fn (string $key) => $key === 'slot')
                    ->map(fn (string $key) => Str::kebab($key))
                    ->all(),
            ]);
        }

        return new HtmlSanitizer($config);
    }

    /**
     * Security risk (opt-in via RAW_HTML, warned in docs): hides data-html-content nodes from the sanitizer.
     */
    private function encodeRawHtml(string $value): string
    {
        $fragment = (new HTML5())->loadHTMLFragment($value);

        // only placeholders created here may be decoded
        $forged = (new DOMXPath($fragment->ownerDocument))->query(
            'descendant::*[@data-encoded-content][not(ancestor-or-self::*[@data-html-content])]',
            $fragment
        );
        foreach ($forged as $node) {
            $node->removeAttribute('data-encoded-content');
        }

        for ($i = 0; $i < $fragment->childNodes->length; $i++) {
            $node = $fragment->childNodes->item($i);
            if ($node instanceof DOMElement && $node->hasAttribute('data-html-content')) {
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

    /**
     * Security risk (opt-in via RAW_HTML, warned in docs): restores data-html-content nodes unsanitized.
     */
    private function decodeRawHtml(string $value): string
    {
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
