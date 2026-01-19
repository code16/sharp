<?php

namespace Code16\Sharp\Utils\Testing\Dashboard;

use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Http\Context\SharpBreadcrumb;
use Code16\Sharp\Show\Fields\SharpShowDashboardField;
use Code16\Sharp\Show\Fields\SharpShowField;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Testing\Commands\FormatsDataForCommand;
use Code16\Sharp\Utils\Testing\Commands\PendingCommand;
use Code16\Sharp\Utils\Testing\IsPendingComponent;
use Code16\Sharp\Utils\Testing\Show\PendingShow;
use Illuminate\Foundation\Testing\TestCase;

class PendingDashboard
{
    use FormatsDataForCommand;
    use IsPendingComponent;

    protected SharpDashboard $dashboard;
    public string $entityKey;
    protected array $filterValues = [];

    public function __construct(
        /** @var TestCase $test */
        protected object $test,
        string $entityKey,
        public ?PendingShow $parent = null,
    ) {
        $this->entityKey = app(SharpEntityManager::class)->entityKeyFor($entityKey);
        $this->dashboard = app(SharpEntityManager::class)->entityFor($this->entityKey)->getViewOrFail();
    }

    public function withFilter(string $filterKey, mixed $value): static
    {
        $key = $this->dashboard->filterContainer()->findFilterHandler($filterKey)->getKey();
        $this->filterValues[$key] = $value;

        return $this;
    }

    public function get(): AssertableDashboard
    {
        $this->setGlobalFilterUrlDefault();

        return new AssertableDashboard(
            $this->parent instanceof PendingShow
                ? $this->test
                    ->getJson(
                        route('code16.sharp.api.dashboard', [
                            'dashboardKey' => $this->entityKey,
                            ...$this->dashboard
                                ->filterContainer()
                                ->getQueryParamsFromFilterValues($this->dashboardShowField()->toArray()['hiddenFilters'] ?? []),
                            ...$this->dashboardQueryParams(),
                        ]),
                        headers: [
                            SharpBreadcrumb::CURRENT_PAGE_URL_HEADER => $this->getCurrentPageUrlFromParents(),
                        ]
                    )
                : $this->test->get(route('code16.sharp.dashboard', [
                    'dashboardKey' => $this->entityKey,
                    ...$this->dashboardQueryParams(),
                ])),
            $this,
        );
    }

    public function dashboardCommand(string $commandKeyOrClassName): PendingCommand
    {
        $this->setGlobalFilterUrlDefault();

        $commandKey = class_exists($commandKeyOrClassName)
            ? class_basename($commandKeyOrClassName)
            : $commandKeyOrClassName;

        return new PendingCommand(
            getForm: fn (?string $step = null) => $this
                ->test
                ->getJson(
                    route(
                        'code16.sharp.api.dashboard.command.form',
                        [
                            'dashboardKey' => $this->entityKey,
                            'commandKey' => $commandKey,
                            'command_step' => $step,
                            ...$this->dashboardQueryParams(),
                        ]
                    ),
                    headers: [
                        SharpBreadcrumb::CURRENT_PAGE_URL_HEADER => $this->getCurrentPageUrlFromParents(),
                    ]
                ),
            post: fn (array $data, ?string $step = null, ?array $baseData = null) => $this
                ->test
                ->postJson(
                    route(
                        'code16.sharp.api.dashboard.command',
                        ['dashboardKey' => $this->entityKey, 'commandKey' => $commandKey]
                    ),
                    [
                        'data' => $this->formatDataForCommand(
                            $this->dashboard->findDashboardCommandHandler($commandKey),
                            $data,
                            $baseData
                        ),
                        'query' => $this->dashboardQueryParams(),
                        'command_step' => $step,
                    ],
                    headers: [
                        SharpBreadcrumb::CURRENT_PAGE_URL_HEADER => $this->getCurrentPageUrlFromParents(),
                    ]
                ),
            commandContainer: $this->dashboard,
        );
    }

    protected function dashboardShowField(): ?SharpShowDashboardField
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->parent instanceof PendingShow
            ? $this->parent->show->getBuiltFields()
                ->first(fn (SharpShowField $field) => $field instanceof SharpShowDashboardField
                    && $field->toArray()['dashboardKey'] === $this->entityKey
                )
            : null;
    }

    protected function dashboardQueryParams(): array
    {
        return $this->dashboard
            ->filterContainer()
            ->getQueryParamsFromFilterValues($this->filterValues)
            ->all();
    }
}
