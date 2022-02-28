<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;
use DOMDocument;
use DOMElement;
use Illuminate\Support\Str;

class EditorFormatter extends SharpFieldFormatter
{
    public function toFront(SharpFormField $field, $value)
    {
        return [
            'text' => $value,
        ];
    }

    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        $content = $value['text'] ?? '';
        $files = $value['files'] ?? [];

        if ($field->isLocalized()) {
            return collect(is_array($content) ? $content : [app()->getLocale() => $content])
                ->union(collect($this->dataLocalizations ?? [])->mapWithKeys(fn ($locale) => [$locale => null]))
                ->map(function (?string $localizedContent) use ($files, $field, $attribute) {
                    return $localizedContent
                        ? preg_replace(
                            '/\R/', "\n",
                            $this->handleUploadedFiles($localizedContent, $files, $field, $attribute),
                        )
                        : null;
                })
                ->toArray();
        }

        return preg_replace('/\R/', "\n", $this->handleUploadedFiles($content, $files, $field, $attribute));
    }

    protected function handleUploadedFiles(string $text, array $files, SharpFormField $field, string $attribute): string
    {
        if (count($files)) {
            $dom = $this->getDomDocument($text);
            $uploadFormatter = app(UploadFormatter::class);

            foreach ($files as $file) {
                $upload = $uploadFormatter
                    ->setInstanceId($this->instanceId)
                    ->fromFront($field, $attribute, $file);

                if (isset($upload['file_name'])) {
                    // New file was uploaded. We have to update the name of the file in the markdown

                    /** @var DOMElement $domElement */
                    $domElement = collect($dom->getElementsByTagName('x-sharp-file'))
                        ->merge($dom->getElementsByTagName('x-sharp-image'))
                        ->first(function (DOMElement $uploadElement) use ($file) {
                            return $uploadElement->getAttribute('name') === $file['name'];
                        });

                    if ($domElement) {
                        $domElement->setAttribute('name', basename($upload['file_name']));
                        $domElement->setAttribute('path', $upload['file_name']);
                        $domElement->setAttribute('disk', $upload['disk']);
                    }
                }
            }

            return $this->formatDomStringValue($dom);
        }

        return $text;
    }

    protected function getDomDocument(string $content): DOMDocument
    {
        return tap(new DOMDocument(), function (DOMDocument $dom) use ($content) {
            $content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
            @$dom->loadHTML("<body>$content</body>");
        });
    }

    protected function formatDomStringValue(DOMDocument $dom): string
    {
        $body = $dom->getElementsByTagName('body')->item(0);

        return trim(Str::replace(['<body>', '</body>'], '', $dom->saveHTML($body)));
    }
}
