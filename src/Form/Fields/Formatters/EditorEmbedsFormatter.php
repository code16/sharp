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
                    $embeds[$embed->key()][] = collect($embed->fieldsContainer()->getFields())
                        ->mapWithKeys(function (SharpFormField $field) use ($element) {
                            if(!$element->hasAttribute(Str::kebab($field->key()))) {
                                return [
                                    $field->key() => null
                                ];
                            }
                            return [
                                $field->key() => $this->tryJsonDecode(
                                    $element->getAttribute(Str::kebab($field->key()))
                                ),
                            ];
                        })
                        ->pipe(function ($data) use ($embed) {
                            return $embed->transformDataForTemplate($data, true);
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
                
                foreach ($field->embeds() as $embed) {
                    $elements = $this->getRootElementsByTagNames($domDocument, [$embed->tagName()]);
                    foreach ($elements as $element) {
                        $dataKey = $element->getAttribute('data-key');
                        foreach ($embed->fieldsContainer()->getFields() as $field) {
                            if($field instanceof SharpFormUploadField) {
                                $field->formatter()
                                    ->setAlwaysReturnFullObject();
                            }
                            if($field instanceof SharpFormListField) {
                                $field->itemFields()
                                    ->whereInstanceOf(SharpFormUploadField::class)
                                    ->each(fn (SharpFormField $field) =>
                                        $field->formatter()->setAlwaysReturnFullObject()
                                    );
                            }
                            $formatted = $field->formatter()
                                ->setInstanceId($this->instanceId)
                                ->fromFront(
                                    $field,
                                    $field->key(),
                                    $value['embeds'][$embed->key()][$dataKey][$field->key()] ?? null
                                );
                            if($formatted !== null) {
                                $element->setAttribute(
                                    Str::kebab($field->key()),
                                    is_array($formatted) ? json_encode($formatted) : $formatted
                                );
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
                        foreach ($embed->fieldsContainer()->getFields() as $field) {
                            if ($field->formatter() instanceof FormatsAfterUpdate
                                && $element->hasAttribute(Str::kebab($field->key()))
                            ) {
                                $formatted = $field->formatter()
                                    ->setInstanceId($this->instanceId)
                                    ->afterUpdate(
                                        $field,
                                        $field->key(),
                                        $this->tryJsonDecode($element->getAttribute(Str::kebab($field->key())))
                                    );
                                
                                $element->setAttribute(
                                    Str::kebab($field->key()),
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
