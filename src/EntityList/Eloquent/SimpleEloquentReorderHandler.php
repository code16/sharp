<?php

namespace Code16\Sharp\EntityList\Eloquent;

use Code16\Sharp\EntityList\Commands\ReorderHandler;

class SimpleEloquentReorderHandler implements ReorderHandler
{
    protected string $idAttribute = 'id';
    protected string $orderAttribute = 'order';

    public function __construct(private string $modelClassName)
    {
    }

    public function setIdAttribute(string $idAttribute): self
    {
        $this->idAttribute = $idAttribute;
        
        return $this;
    }

    public function setOrderAttribute(string $orderAttribute): self
    {
        $this->orderAttribute = $orderAttribute;

        return $this;
    }

    public function reorder(array $ids): void
    {
        (new ($this->modelClassName))
            ->whereIn($this->idAttribute, $ids)
            ->get()
            ->each(function ($instance) use ($ids) {
                $instance->{$this->orderAttribute} = array_search($instance->{$this->idAttribute}, $ids) + 1;
                $instance->save();
            });
    }
}