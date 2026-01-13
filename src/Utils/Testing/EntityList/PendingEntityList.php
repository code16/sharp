<?php

namespace Code16\Sharp\Utils\Testing\EntityList;

use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Http\Context\SharpBreadcrumb;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Testing\Commands\AssertableCommand;
use Code16\Sharp\Utils\Testing\IsPendingComponent;
use Code16\Sharp\Utils\Testing\Show\PendingShow;
use Illuminate\Foundation\Testing\TestCase;

class PendingEntityList
{
    use IsPendingComponent;

    protected SharpEntityList $entityList;
    public string $entityKey;
    protected array $filterValues = [];

    public function __construct(
        /** @var TestCase $test */
        protected object $test,
        string $entityKey,
        public ?PendingShow $parent = null,
    ) {
        $this->entityKey = app(SharpEntityManager::class)->entityKeyFor($entityKey);
        $this->entityList = app(SharpEntityManager::class)->entityFor($this->entityKey)->getListOrFail();
    }

    public function sharpShow(string $entityKey, string|int $instanceId): PendingShow
    {
        return new PendingShow(
            $this->test,
            $entityKey,
            $instanceId,
            parent: $this->parent instanceof PendingShow ? $this->parent : $this
        );
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
            fn ($data, $step) => $this
                ->test
                ->withHeader(
                    SharpBreadcrumb::CURRENT_PAGE_URL_HEADER,
                    $this->getCurrentPageUrlFromParents(),
                )
                ->postJson(
                    route(
                        'code16.sharp.api.list.command.entity',
                        ['entityKey' => $this->entityKey, 'commandKey' => $commandKey]
                    ),
                    [
                        'data' => $data,
                        'query' => $this->entityListQueryParams(),
                        'command_step' => $step,
                    ],
                ),
            commandContainer: $this->entityList,
            data: $data,
            step: $commandStep
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
            fn ($data, $step) => $this
                ->test
                ->withHeader(
                    SharpBreadcrumb::CURRENT_PAGE_URL_HEADER,
                    $this->getCurrentPageUrlFromParents(),
                )
                ->postJson(
                    route(
                        'code16.sharp.api.list.command.instance',
                        ['entityKey' => $this->entityKey, 'instanceId' => $instanceId, 'commandKey' => $commandKey]
                    ),
                    [
                        'data' => $data,
                        'query' => $this->entityListQueryParams(),
                        'command_step' => $step,
                    ],
                ),
            commandContainer: $this->entityList,
            data: $data,
            step: $commandStep,
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
