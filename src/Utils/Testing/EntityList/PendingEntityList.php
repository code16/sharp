<?php

namespace Code16\Sharp\Utils\Testing\EntityList;

use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Testing\GeneratesSharpUrl;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;

class PendingEntityList
{
    use GeneratesSharpUrl;

    protected SharpEntityList $entityList;
    protected array $filterValues = [];

    public function __construct(
        /** @var MakesHttpRequests $test */
        protected object $test,
        protected string $entityKey
    ) {
        $resolvedEntityKey = app(SharpEntityManager::class)->entityKeyFor($this->entityKey);
        $this->entityList = app(SharpEntityManager::class)->entityFor($resolvedEntityKey)->getListOrFail();
    }

    public function withFilter(string $filterKey, mixed $value): static
    {
        $key = $this->entityList->filterContainer()->findFilterHandler($filterKey)->getKey();
        $this->filterValues[$key] = $value;

        return $this;
    }

    public function get(): AssertableEntityList
    {
        $this->setGlobalFilterUrlDefault();

        return new AssertableEntityList(
            $this->test->get(
                route('code16.sharp.list', [
                    'entityKey' => $this->entityKey,
                    ...$this->entityList
                        ->filterContainer()
                        ->getQueryParamsFromFilterValues($this->filterValues),
                ])
            )
        );
    }

    public function callInstanceCommand() {}

    public function callEntityCommand() {}
}
