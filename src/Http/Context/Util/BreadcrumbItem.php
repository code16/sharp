<?php

namespace Code16\Sharp\Http\Context\Util;

use Code16\Sharp\Form\SharpSingleForm;
use Code16\Sharp\Show\SharpSingleShow;

class BreadcrumbItem
{
    /** @var string */
    public $type;
    
    /** @var string */
    public $key;
    
    /** @var string */
    public $instance;

    /** @var int */
    public $depth;

    public function __construct(string $type, string $key)
    {
        $this->type = $type;
        $this->key = $key;
    }

    public function setDepth(int $depth): self
    {
        $this->depth = $depth;
        return $this;
    }

    public function setInstance(?string $instance): self
    {
        $this->instance = $instance;
        return $this;
    }

    public function isShow(): bool
    {
        return $this->type === "s-show";
    }

    public function isSingleShow(): bool
    {
        return $this->isShow() 
            && $this->instanceId() === null
            && is_subclass_of(config("sharp.entities.{$this->entityKey()}.show"), SharpSingleShow::class);
    }

    public function isForm(): bool
    {
        return $this->type === "s-form";
    }

    public function isSingleForm(): bool
    {
        return $this->isForm() 
            && $this->instanceId() === null
            && is_subclass_of(config("sharp.entities.{$this->entityKey()}.form"), SharpSingleForm::class);
    }

    public function entityKey(): string
    {
        return $this->key;
    }

    public function instanceId(): ?string
    {
        return $this->instance;
    }
}