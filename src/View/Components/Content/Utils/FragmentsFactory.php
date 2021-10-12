<?php


namespace Code16\Sharp\View\Components\Content\Utils;


use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Pure;

class FragmentsFactory
{
    
    public function __construct()
    {
    }
    
    public function fromHTML(string $html): Collection
    {
        $doc = new \DOMDocument();

        libxml_use_internal_errors(true);
        $doc->loadHTML($html);
        $container = $this->findContentContainer($doc);

        return $this->fromDOMNode($container);
    }
    
    protected function fromDOMNode(\DOMNode $container): Collection
    {
        $fragments = collect();
        
        foreach ($container->childNodes as $node) {
            if($componentElement = $this->findComponentElement($node)) {
                $fragments->push(ComponentFragment::fromDOMElement($componentElement));
                continue;
            }
            
            $lastFragment = $fragments->last();
            if($lastFragment instanceof HTMLFragment) {
                $lastFragment->appendDOMNode($node);
            } else {
                $fragments->push(HTMLFragment::fromDOMNode($node));
            }
        }
        
        return $fragments;
    }
    
    protected function findContentContainer(\DOMDocument $document): \DOMElement
    {
        return $document->getElementsByTagName('body')->item(0);
    }
    
    #[Pure]
    protected function isComponentElement(?\DOMNode $node): bool
    {
        return $node instanceof \DOMElement
            && Str::startsWith($node->tagName, 'x-');
    }
    
    #[Pure]
    protected function findComponentElement(\DOMNode $node): ?\DOMElement
    {
        if($this->isComponentElement($node)) {
            return $node;
        }
        
        if($this->isComponentElement($node->firstChild)) {
            return $node->firstChild;
        }
        
        return null;
    }
}
