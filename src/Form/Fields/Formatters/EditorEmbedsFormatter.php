<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Utils\Fields\Formatters\FormatsEditorEmbeds;
use Code16\Sharp\Utils\Fields\Formatters\FormatsHtmlContent;
use Illuminate\Support\Str;

class EditorEmbedsFormatter extends SharpFieldFormatter implements FormatsAfterUpdate
{
    use FormatsEditorEmbeds;
    use FormatsHtmlContent;

    /**
     * @param  SharpFormEditorField  $field
     */
    public function toFront(SharpFormField $field, $value)
    {
        return $this->formatEditorEmbedsToFront($field, $value);
    }

    /**
     * @param  SharpFormEditorField  $field
     */
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        if (! count($field->embeds())) {
            return $value['text'] ?? null;
        }

        return $this->maybeLocalized(
            $field,
            $value['text'] ?? null,
            function (string $content) use ($field, $value) {
                $domDocument = $this->parseHtml($content);

                foreach ($field->embeds() as $embedKey => $embed) {
                    $elements = $this->getRootElementsByTagNames($domDocument, [$embed->tagName()]);
                    foreach ($elements as $element) {
                        $dataKey = $element->getAttribute('data-key');
                        foreach ($embed->getBuiltFields() as $fieldKey => $field) {
                            $embedData = ((array) $value['embeds'][$embedKey])[$dataKey];
                            $fieldValue = $embedData[$fieldKey] ?? null;
                            if ($field->formatter() instanceof UploadFormatter) {
                                $fieldValue = $field->formatter()
                                    ->setInstanceId($this->instanceId)
                                    ->fromFront($field, $fieldKey, $fieldValue);
                            }
                            if ($field->formatter() instanceof ListFormatter) {
                                $fieldValue = $field->formatter()
                                    ->formatItemFieldUsing(function (SharpFormField $field) {
                                        if ($field instanceof SharpFormUploadField) {
                                            return $field->formatter();
                                        }

                                        // other field types have already been formatted, so we pass value through
                                        return new class() extends AbstractSimpleFormatter {};
                                    })
                                    ->setInstanceId($this->instanceId)
                                    ->fromFront($field, $fieldKey, $fieldValue);
                            }
                            if ($fieldValue !== null) {
                                if ($fieldKey === 'slot') {
                                    $this->setInnerHtml($element, $fieldValue);
                                } else {
                                    $element->setAttribute(
                                        Str::kebab($fieldKey),
                                        is_array($fieldValue) ? json_encode($fieldValue) : $fieldValue
                                    );
                                }
                            }
                        }
                        $element->removeAttribute('data-key');
                        $element->removeAttribute('data-value');
                    }
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
        if (! count($field->embeds())) {
            return $value;
        }

        return $this->maybeLocalized(
            $field,
            $value,
            function (string $content) use ($field) {
                $domDocument = $this->parseHtml($content);

                foreach ($field->embeds() as $embed) {
                    $elements = $this->getRootElementsByTagNames($domDocument, [$embed->tagName()]);
                    foreach ($elements as $element) {
                        foreach ($embed->getBuiltFields() as $fieldKey => $field) {
                            if ($field->formatter() instanceof FormatsAfterUpdate
                                && $element->hasAttribute(Str::kebab($fieldKey))
                            ) {
                                $formatted = $field->formatter()
                                    ->setInstanceId($this->instanceId)
                                    ->afterUpdate(
                                        $field,
                                        $fieldKey,
                                        $this->tryJsonDecode($element->getAttribute(Str::kebab($fieldKey)))
                                    );

                                $element->setAttribute(
                                    Str::kebab($fieldKey),
                                    is_array($formatted) ? json_encode($formatted) : $formatted
                                );
                            }
                        }
                    }
                }

                return $this->toHtml($domDocument);
            }
        );
    }
}
