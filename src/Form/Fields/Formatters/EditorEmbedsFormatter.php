<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use DOMAttr;
use DOMNode;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EditorEmbedsFormatter extends SharpFieldFormatter implements FormatsAfterUpdate
{
    use HasMaybeLocalizedValue;
    use HandlesHtmlContent;
    
    /**
     * @param SharpFormEditorField $field
     */
    public function toFront(SharpFormField $field, $value)
    {
        if(!count($field->embeds())) {
            return ['text' => $value];
        }
        
        $embeds = [];

        $text = $this->maybeLocalized($field, $value, function (string $content) use (&$embeds, $field) {
            $domDocument = $this->parseHtml($content);
            
            foreach ($field->embeds() as $embed) {
                $elements = $this->getRootElementsByTagNames($domDocument, [$embed->tagName()]);
                foreach ($elements as $element) {
                    $embeds[$embed->key()][] = $embed->getBuiltFields()
                        ->map(function (SharpFormField $field, $fieldKey) use ($element) {
                            if($fieldKey === 'slot') {
                                return tap($this->getInnerHtml($element), function () use ($element) {
                                    $this->setInnerHtml($element, '');
                                });
                            }
                            return $element->hasAttribute(Str::kebab($fieldKey))
                                ? $this->tryJsonDecode($element->getAttribute(Str::kebab($fieldKey)))
                                : null;
                        })
                        ->pipe(function ($collection) use ($embed) {
                            return $embed->transformDataForTemplate($collection->toArray(), true);
                        });
                    
                    // remove all attributes as not needed by the front
                    collect($element->attributes)
                        ->each(function (DOMAttr $attribute) use ($element) {
                            $element->removeAttribute($attribute->name);
                        });
                    
                    $element->setAttribute('data-key', count($embeds[$embed->key()]) - 1);
                }
            }

            return $this->getHtml($domDocument);
        });

        return [
            'text' => $text,
            ...count($embeds) ? [
                'embeds' => $embeds,
            ] : [],
        ];
    }

    /**
     * @param SharpFormEditorField $field
     */
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        if(!count($field->embeds())) {
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
                            $fieldValue = $value['embeds'][$embedKey][$dataKey][$fieldKey] ?? null;
                            if($field instanceof SharpFormUploadField) {
                                $fieldValue = $field->formatter()
                                    ->setInstanceId($this->instanceId)
                                    ->setAlwaysReturnFullObject()
                                    ->fromFront($field, $fieldKey, $fieldValue);
                            }
                            if($field instanceof SharpFormListField) {
                                $fieldValue = $field->formatter()
                                    ->formatItemFieldUsing(function (SharpFormField $field) {
                                        if ($field instanceof SharpFormUploadField) {
                                            return $field->formatter()->setAlwaysReturnFullObject();
                                        }
                                        // other field types have already been formatted, so we pass value through
                                        return new class extends AbstractSimpleFormatter
                                        {
                                        };
                                    })
                                    ->setInstanceId($this->instanceId)
                                    ->fromFront($field, $fieldKey, $fieldValue);
                            }
                            if($fieldValue !== null) {
                                if($fieldKey === 'slot') {
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
                    }
                }
                
                return $this->getHtml($domDocument);
            }
        );
    }

    /**
     * @param SharpFormEditorField $field
     */
    public function afterUpdate(SharpFormField $field, string $attribute, mixed $value): ?string
    {
        if(!count($field->embeds())) {
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
                
                return $this->getHtml($domDocument);
            }
        );
    }
    
    protected function tryJsonDecode(?string $elementAttributeValue): mixed
    {
        if($elementAttributeValue === null) {
            return null;
        }
        
        $decoded = json_decode($elementAttributeValue, true);
        
        if(json_last_error() !== JSON_ERROR_NONE) {
            return $elementAttributeValue;
        }
        
        return $decoded;
    }
}
