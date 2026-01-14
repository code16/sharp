<?php

namespace Code16\Sharp\Utils\Testing\EntityList;

use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Http\Context\SharpBreadcrumb;
use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Show\Fields\SharpShowField;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Testing\Commands\AssertableCommand;
use Code16\Sharp\Utils\Testing\Form\PendingForm;
use Code16\Sharp\Utils\Testing\IsPendingComponent;
use Code16\Sharp\Utils\Testing\Show\PendingShow;
use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Assert as PHPUnit;

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

        if ($this->parent instanceof PendingShow) {
            PHPUnit::assertNotNull($this->entityListShowField(), sprintf('Unknown entity list show field "%s"', $this->entityKey));
        }
    }

    public function sharpShow(string $entityClassNameOrKey, string|int $instanceId): PendingShow
    {
        return new PendingShow($this->test, $entityClassNameOrKey, $instanceId, parent: $this);
    }

    public function sharpForm(string $entityClassNameOrKey, string|int $instanceId): PendingForm
    {
        return new PendingForm($this->test, $entityClassNameOrKey, $instanceId, parent: $this);
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
            $this->parent instanceof PendingShow
                ? $this->test
                    ->withHeader(
                        SharpBreadcrumb::CURRENT_PAGE_URL_HEADER,
                        $this->getCurrentPageUrlFromParents(),
                    )
                    ->get(
                        route('code16.sharp.api.list', [
                            'entityKey' => $this->entityKey,
                            ...$this->entityList
                                ->filterContainer()
                                ->getQueryParamsFromFilterValues($this->entityListShowField()->toArray()['hiddenFilters'] ?? []),
                            ...$this->entityListQueryParams(),
                        ])
                    )
                : $this->test->get(route('code16.sharp.list', [
                        'entityKey' => $this->entityKey,
                        ...$this->entityListQueryParams(),
                    ])),
            $this,
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

    protected function entityListShowField(): ?SharpShowEntityListField
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->parent instanceof PendingShow
            ? $this->parent->show->getBuiltFields()
                ->first(fn (SharpShowField $field) => $field instanceof SharpShowEntityListField
                    && $field->toArray()['entityListKey'] === $this->entityKey
                )
            : null;
    }

    protected function entityListQueryParams(): array
    {
        return $this->entityList
            ->filterContainer()
            ->getQueryParamsFromFilterValues($this->filterValues)
            ->all();
    }
}
