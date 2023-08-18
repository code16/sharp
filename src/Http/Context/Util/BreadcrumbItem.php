<?php

namespace Code16\Sharp\Http\Context\Util;

use Code16\Sharp\Utils\Entities\SharpEntityManager;

class BreadcrumbItem
{
    public string $type;
    public string $key;
    public ?string $instance = null;
    public int $depth = 0;

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

    public function isEntityList(): bool
    {
        return $this->type === 's-list';
    }

    public function isShow(): bool
    {
        return $this->type === 's-show';
    }

    public function isSingleShow(): bool
    {
        return $this->isShow()
            && $this->instanceId() === null
            && app(SharpEntityManager::class)->entityFor($this->entityKey())->isSingle();
    }

    public function isForm(): bool
    {
        return $this->type === 's-form';
    }

    public function isSingleForm(): bool
    {
        return $this->isForm()
            && $this->instanceId() === null
            && app(SharpEntityManager::class)->entityFor($this->entityKey())->isSingle();
    }

    public function is(BreadcrumbItem $item): bool
    {
        return $this->type === $item->type
            && $this->key === $item->key
            && $this->instance === $item->instance;
    }

    public function entityKey(): string
    {
        return $this->key;
    }

    public function instanceId(): ?string
    {
        return $this->instance;
    }

    public function toUri(): string
    {
        return sprintf('%s/%s',
            $this->type,
            isset($this->instance)
                ? $this->key.'/'.$this->instance
                : $this->key,
        );
    }
}
