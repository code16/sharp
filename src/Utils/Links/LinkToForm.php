<?php

namespace Code16\Sharp\Utils\Links;

class LinkToForm extends LinkToShowPage
{
    protected bool $throughShowPage = false;

    public function throughShowPage($throughShowPage = true): self
    {
        $this->throughShowPage = $throughShowPage;
        
        return $this;
    }
    
    protected function generateUri(): string
    {
        $uri = sprintf("s-form/%s/%s", $this->entityKey, $this->instanceId);
        
        if($this->throughShowPage) {
            $uri = sprintf("%s/%s", parent::generateUri(), $uri);
        }
        
        return $uri;
    }
}