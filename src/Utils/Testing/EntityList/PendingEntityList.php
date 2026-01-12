<?php

namespace Code16\Sharp\Utils\Testing\EntityList;

use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Http\Context\SharpBreadcrumb;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Testing\Commands\AssertableCommand;
use Code16\Sharp\Utils\Testing\GeneratesSharpUrl;
use Illuminate\Foundation\Testing\TestCase;

class PendingEntityList
{
    use GeneratesSharpUrl;

    protected SharpEntityList $entityList;
    protected array $filterValues = [];
    protected string $entityKey;

    public function __construct(
        /** @var TestCase $test */
        protected object $test,
        string $entityKey
    ) {
        $this->entityKey = app(SharpEntityManager::class)->entityKeyFor($entityKey);
        $this->entityList = app(SharpEntityManager::class)->entityFor($this->entityKey)->getListOrFail();
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
                    ...$this->entityListQueryParams(),
                ])
            )
        );
    }

    public function callEntityCommand(
        string $commandKeyOrClassName,
        array $data = [],
        ?string $commandStep = null
    ): AssertableCommand {
        $this->setGlobalFilterUrlDefault();

        $commandKey = class_exists($commandKeyOrClassName)
            ? class_basename($commandKeyOrClassName)
            : $commandKeyOrClassName;

        return new AssertableCommand(
            $this
                ->test
                ->withHeader(
                    SharpBreadcrumb::CURRENT_PAGE_URL_HEADER,
                    $this->buildCurrentPageUrl(
                        $this->breadcrumbBuilder($this->entityKey)
                    ),
                )
                ->postJson(
                    route(
                        'code16.sharp.api.list.command.entity',
                        ['entityKey' => $this->entityKey, 'commandKey' => $commandKey]
                    ),
                    [
                        'data' => $data,
                        'query' => $this->entityListQueryParams(),
                        'command_step' => $commandStep,
                    ],
                )
        );
    }

    public function callInstanceCommand(
        int|string $instanceId,
        string $commandKeyOrClassName,
        array $data = [],
        ?string $commandStep = null
    ): AssertableCommand {
        $this->setGlobalFilterUrlDefault();

        $commandKey = class_exists($commandKeyOrClassName)
            ? class_basename($commandKeyOrClassName)
            : $commandKeyOrClassName;

        return new AssertableCommand(
            $this
                ->test
                ->withHeader(
                    SharpBreadcrumb::CURRENT_PAGE_URL_HEADER,
                    $this->buildCurrentPageUrl(
                        $this->breadcrumbBuilder($this->entityKey, $instanceId)
                    ),
                )
                ->postJson(
                    route(
                        'code16.sharp.api.list.command.instance',
                        ['entityKey' => $this->entityKey, 'instanceId' => $instanceId, 'commandKey' => $commandKey]
                    ),
                    [
                        'data' => $data,
                        'query' => $this->entityListQueryParams(),
                        'command_step' => $commandStep,
                    ],
                )
        );
    }

    protected function entityListQueryParams(): array
    {
        return $this->entityList
            ->filterContainer()
            ->getQueryParamsFromFilterValues($this->filterValues)
            ->all();
    }
}
