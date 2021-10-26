<?php

namespace Code16\Sharp\View\Components\Content\Utils;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Pure;

class FragmentsFactory
{
    public function fromHTML(string $html): Collection
    {
        $doc = new \DOMDocument();

        libxml_use_internal_errors(true);
        $doc->loadHTML("<body>$html</body>");
        $container = $this->findContentContainer($doc);
        $fragments = $this->fromDOMNode($container);
        
        if($boundaries = $this->getContainerBoundaries($container)) {
            [$start, $end] = $boundaries;
            $fragments->prepend(new HTMLFragment($start));
            $fragments->push(new HTMLFragment($end));
        }

        return $this->groupFragments($fragments);
    }
    
    protected function fromDOMNode(\DOMElement $container): Collection
    {
        $fragments = collect();
        
        foreach ($container->childNodes as $node) {
            if($componentElement = $this->findComponentElement($node)) {
                $fragments->push(ComponentFragment::fromDOMElement($componentElement));
            } else {
                $fragments->push(HTMLFragment::fromDOMNode($node));
            }
        }
        
        return $fragments;
    }
    
    protected function groupFragments(Collection $fragments): Collection
    {
        return $fragments
            ->chunkWhile(function ($fragment, $key, $chunk) {
                return $fragment->type == $chunk->last()->type;
            })
            ->map(function ($fragments) {
                if($fragments->first() instanceof HTMLFragment) {
                    return new HTMLFragment(
                        collect($fragments)
                            ->map(fn ($fragment) => $fragment->getHTML())
                            ->join('')
                    );
                }
                return $fragments;
            })
            ->flatten();
    }
    
    protected function getContainerBoundaries(\DOMElement $container): ?array
    {
        if($container->tagName === 'body') {
            return null;
        }
        
        $container->insertBefore(new \DOMComment('content-start'), $container->firstChild);
        $container->appendChild(new \DOMComment('content-end'));
        $body = $container->ownerDocument->getElementsByTagName('body')->item(0);
        $html = $container->ownerDocument->saveHTML($body);
        $html = Str::between($html, '<body>', '</body>');
        
        return [
            Str::before($html, '<!--content-start'),
            Str::after($html, 'content-end-->'),
        ];
    }
    
    protected function findContentContainer(\DOMDocument $document): \DOMNode
    {
        if($container = (new \DOMXPath($document))->query('//div[not(div)]')->item(0)) {
            return $container; // is most deep <div>
        }
        
        return $document->getElementsByTagName('body')->item(0);
    }
    
    #[Pure]
    protected function isComponentElement(?\DOMNode $node): bool
    {
        return $node instanceof \DOMElement
            && Str::startsWith($node->tagName, 'x-');
    }
    
    #[Pure]
    protected function findComponentElement(\DOMNode $node): ?\DOMNode
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
