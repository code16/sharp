<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Utils\Fields\Formatters\FormatsEditorUploads;
use Code16\Sharp\Utils\Fields\Formatters\FormatsHtmlContent;

class EditorUploadsFormatter extends SharpFieldFormatter implements FormatsAfterUpdate
{
    use FormatsEditorUploads;
    use FormatsHtmlContent;

    /**
     * @param  SharpFormEditorField  $field
     */
    public function toFront(SharpFormField $field, $value)
    {
        if (! $field->uploadsConfig()) {
            return ['text' => $value];
        }

        return $this->formatEditorUploadsToFront($field, $value);
    }

    /**
     * @param  SharpFormEditorField  $field
     */
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        if (! $field->uploadsConfig()) {
            return $value['text'] ?? null;
        }

        return $this->maybeLocalized(
            $field,
            $value['text'] ?? null,
            function (string $content) use ($field, $value) {
                $domDocument = $this->parseHtml($content);

                foreach ($this->getUploadElements($domDocument) as $element) {
                    $key = $element->getAttribute('data-key');
                    $file = $field
                        ->uploadsConfig()
                        ->formatter()
                        ->setInstanceId($this->instanceId)
                        ->fromFront($field->uploadsConfig(), 'file', $value['uploads'][$key]['file']);
                    $element->setAttribute('file', json_encode($file));
                    if ($legend = $value['uploads'][$key]['legend'] ?? null) {
                        $element->setAttribute('legend', $legend);
                    }
                    $element->removeAttribute('data-key');
                }

                return $this->toHtml($domDocument);
            }
        );
    }

    /**
     * @param  SharpFormEditorField  $field
     */
    public function afterUpdate(SharpFormField $field, string $attribute, mixed $value): array|string|null
    {
        if (! $field->uploadsConfig()) {
            return $value;
        }

        return $this->maybeLocalized(
            $field,
            $value,
            function (string $content) use ($field) {
                $domDocument = $this->parseHtml($content);

                foreach ($this->getUploadElements($domDocument) as $element) {
                    $file = json_decode($element->getAttribute('file'), true);
                    $file = $field
                        ->uploadsConfig()
                        ->formatter()
                        ->setInstanceId($this->instanceId)
                        ->afterUpdate($field->uploadsConfig(), 'file', $file);

                    $element->setAttribute('file', json_encode($file));
                }

                return $this->toHtml($domDocument);
            }
        );
    }
}
