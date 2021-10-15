<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;
use DOMDocument;
use DOMElement;

class MarkdownFormatter extends SharpFieldFormatter
{
    function toFront(SharpFormField $field, $value)
    {
        return [
            "text" => $value,
        ];
    }

    function fromFront(SharpFormField $field, string $attribute, $value)
    {
        $text = $value['text'] ?? '';

        if(count($value["files"] ?? [])) {
            $dom = $this->getDomDocument($text);
            $uploadFormatter = app(UploadFormatter::class);

            foreach($value["files"] as $file) {
                $upload = $uploadFormatter
                    ->setInstanceId($this->instanceId)
                    ->fromFront($field, $attribute, $file);

                if(isset($upload["file_name"])) {
                    // New file was uploaded. We have to update the name of the file in the markdown
                    
                    /** @var DOMElement $domElement */
                    $domElement = collect($dom->getElementsByTagName('x-sharp-media'))
                        ->merge($dom->getElementsByTagName('x-sharp-image'))
                        ->first(function(DOMElement $uploadElement) use ($file) {
                            return $uploadElement->getAttribute("name") === $file["name"];
                        });
                    
                    if($domElement) {
                        $domElement->setAttribute("name", basename($upload["file_name"]));
                        $domElement->setAttribute("path", $upload["file_name"]);
                        $domElement->setAttribute("disk", $upload["disk"]);
                    }
                }
            }
            
            $text = $this->formatDomStringValue($dom);
        }
        
        // Normalize \n
        return preg_replace('/\R/', "\n", $text);
    }
    
    protected function getDomDocument(string $content): DOMDocument
    {
        return tap(new DOMDocument(), function(DOMDocument $dom) use ($content) {
            @$dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        });
    }

    protected function formatDomStringValue(DOMDocument $dom): string
    {
        $wrapperElement = $dom->firstChild;
        $newParent = $wrapperElement->parentNode;
        foreach ($wrapperElement->childNodes as $child) {
            $newParent->insertBefore(
                $child->cloneNode(true),
                $wrapperElement
            );
        }
        $newParent->removeChild($wrapperElement);
        
        return trim($dom->saveHTML());
    }
}
