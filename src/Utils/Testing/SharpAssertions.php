<?php

namespace Code16\Sharp\Utils\Testing;

use Closure;
use Code16\Sharp\Filters\GlobalFilters\GlobalFilters;
use Code16\Sharp\Http\Context\SharpBreadcrumb;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Links\BreadcrumbBuilder;
use Illuminate\Support\Facades\URL;

trait SharpAssertions
{
    private BreadcrumbBuilder $breadcrumbBuilder;
    private ?string $filterKey = null;

    /**
     * @deprecated use withSharpBreadcrumb() instead
     */
    public function withSharpCurrentBreadcrumb(...$breadcrumb): self
    {
        $this->breadcrumbBuilder = new BreadcrumbBuilder();

        collect($breadcrumb)
            ->each(fn (array $segment) => match ($segment[0]) {
                'list' => $this->breadcrumbBuilder->appendEntityList($segment[1]),
                'show' => (count($segment) == 2)
                    ? $this->breadcrumbBuilder->appendSingleShowPage($segment[1])
                    : $this->breadcrumbBuilder->appendShowPage($segment[1], $segment[2]),
            });

        return $this;
    }

    /**
     * @param  (\Closure(BreadcrumbBuilder): BreadcrumbBuilder)  $callback
     * @return $this
     */
    public function withSharpBreadcrumb(Closure $callback): self
    {
        $this->breadcrumbBuilder = $callback(new BreadcrumbBuilder());

        return $this;
    }

    public function withSharpGlobalFilterKeys(array|string $filterKeys): self
    {
        $this->filterKey = collect((array) $filterKeys)
            ->implode(GlobalFilters::$valuesUrlSeparator);

        return $this;
    }

    public function deleteFromSharpShow(string $entityClassNameOrKey, mixed $instanceId)
    {
        URL::defaults(['filterKey' => $this->filterKey ?: GlobalFilters::$defaultKey]);

        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        return $this
            ->delete(
                route(
                    'code16.sharp.show.delete', [
                        'filterKey' => $this->filterKey,
                        'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                        'entityKey' => $entityKey,
                        'instanceId' => $instanceId,
                    ]
                ),
            );
    }

    public function deleteFromSharpList(string $entityClassNameOrKey, mixed $instanceId)
    {
        URL::defaults(['filterKey' => $this->filterKey ?: GlobalFilters::$defaultKey]);

        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        return $this
            ->withHeader(
                SharpBreadcrumb::CURRENT_PAGE_URL_HEADER,
                $this->buildCurrentPageUrl(
                    $this->breadcrumbBuilder($entityKey)
                ),
            )
            ->delete(
                route('code16.sharp.api.list.delete', [
                    'filterKey' => $this->filterKey,
                    'entityKey' => $entityKey,
                    'instanceId' => $instanceId,
                ])
            );
    }

    public function getSharpForm(string $entityClassNameOrKey, mixed $instanceId = null)
    {
        URL::defaults(['filterKey' => $this->filterKey ?: GlobalFilters::$defaultKey]);

        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        return $this
            ->get(
                $instanceId
                    ? route(
                        'code16.sharp.form.edit',
                        [
                            'filterKey' => $this->filterKey,
                            'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                            'entityKey' => $entityKey,
                            'instanceId' => $instanceId,
                        ]
                    )
                    : route(
                        'code16.sharp.form.create',
                        [
                            'filterKey' => $this->filterKey,
                            'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                            'entityKey' => $entityKey,
                        ]
                    ),
            );
    }

    public function getSharpSingleForm(string $entityClassNameOrKey)
    {
        URL::defaults(['filterKey' => $this->filterKey ?: GlobalFilters::$defaultKey]);

        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        return $this
            ->get(
                route(
                    'code16.sharp.form.edit',
                    [
                        'filterKey' => $this->filterKey,
                        'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                        'entityKey' => $entityKey,
                    ]
                )
            );
    }

    public function updateSharpForm(string $entityClassNameOrKey, $instanceId, array $data)
    {
        URL::defaults(['filterKey' => $this->filterKey ?: GlobalFilters::$defaultKey]);

        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        return $this
            ->post(
                route(
                    'code16.sharp.form.update', [
                        'filterKey' => $this->filterKey,
                        'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                        'entityKey' => $entityKey,
                        'instanceId' => $instanceId,
                    ]
                ),
                $data,
            );
    }

    public function updateSharpSingleForm(string $entityClassNameOrKey, array $data)
    {
        URL::defaults(['filterKey' => $this->filterKey ?: GlobalFilters::$defaultKey]);

        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        return $this
            ->post(
                route(
                    'code16.sharp.form.update', [
                        'filterKey' => $this->filterKey,
                        'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                        'entityKey' => $entityKey,
                    ]
                ),
                $data,
            );
    }

    public function getSharpShow(string $entityClassNameOrKey, $instanceId)
    {
        URL::defaults(['filterKey' => $this->filterKey ?: GlobalFilters::$defaultKey]);

        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        return $this
            ->get(
                route(
                    'code16.sharp.show.show',
                    [
                        'filterKey' => $this->filterKey,
                        'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                        'entityKey' => $entityKey,
                        'instanceId' => $instanceId,
                    ]
                ),
            );
    }

    public function storeSharpForm(string $entityClassNameOrKey, array $data)
    {
        URL::defaults(['filterKey' => $this->filterKey ?: GlobalFilters::$defaultKey]);

        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        return $this
            ->post(
                route(
                    'code16.sharp.form.store',
                    [
                        'filterKey' => $this->filterKey,
                        'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                        'entityKey' => $entityKey,
                    ]
                ),
                $data,
            );
    }

    public function callSharpInstanceCommandFromList(
        string $entityClassNameOrKey,
        $instanceId,
        string $commandKeyOrClassName,
        array $data = [],
        ?string $commandStep = null
    ) {
        URL::defaults(['filterKey' => $this->filterKey ?: GlobalFilters::$defaultKey]);

        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        $commandKey = class_exists($commandKeyOrClassName)
            ? class_basename($commandKeyOrClassName)
            : $commandKeyOrClassName;

        return $this
            ->withHeader(
                SharpBreadcrumb::CURRENT_PAGE_URL_HEADER,
                $this->buildCurrentPageUrl(
                    $this->breadcrumbBuilder($entityKey)
                ),
            )
            ->postJson(
                route(
                    'code16.sharp.api.list.command.instance',
                    compact('entityKey', 'instanceId', 'commandKey'),
                ),
                ['data' => $data, 'command_step' => $commandStep],
            );
    }

    public function callSharpInstanceCommandFromShow(
        string $entityClassNameOrKey,
        $instanceId,
        string $commandKeyOrClassName,
        array $data = [],
        ?string $commandStep = null
    ) {
        URL::defaults(['filterKey' => $this->filterKey ?: GlobalFilters::$defaultKey]);

        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        $commandKey = class_exists($commandKeyOrClassName)
            ? class_basename($commandKeyOrClassName)
            : $commandKeyOrClassName;

        return $this
            ->withHeader(
                SharpBreadcrumb::CURRENT_PAGE_URL_HEADER,
                $this->buildCurrentPageUrl(
                    $this->breadcrumbBuilder($entityKey, $instanceId)
                ),
            )
            ->postJson(
                route(
                    'code16.sharp.api.show.command.instance',
                    compact('entityKey', 'instanceId', 'commandKey'),
                ),
                ['data' => $data, 'command_step' => $commandStep],
            );
    }

    public function callSharpEntityCommandFromList(
        string $entityClassNameOrKey,
        string $commandKeyOrClassName,
        array $data = [],
        ?string $commandStep = null
    ) {
        URL::defaults(['filterKey' => $this->filterKey ?: GlobalFilters::$defaultKey]);

        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        $commandKey = class_exists($commandKeyOrClassName)
            ? class_basename($commandKeyOrClassName)
            : $commandKeyOrClassName;

        return $this
            ->withHeader(
                SharpBreadcrumb::CURRENT_PAGE_URL_HEADER,
                $this->buildCurrentPageUrl($this->breadcrumbBuilder($entityKey))
            )
            ->postJson(
                route('code16.sharp.api.list.command.entity', compact('entityKey', 'commandKey')),
                ['data' => $data, 'command_step' => $commandStep],
            );
    }

    public function loginAsSharpUser($user): self
    {
        return $this->actingAs($user, sharp()->config()->get('auth.guard') ?: config('auth.defaults.guard'));
    }

    private function breadcrumbBuilder(string $entityKey, ?string $instanceId = null): BreadcrumbBuilder
    {
        if (isset($this->breadcrumbBuilder)) {
            return $this->breadcrumbBuilder;
        }

        return (new BreadcrumbBuilder())
            ->appendEntityList($entityKey)
            ->when($instanceId, fn ($builder) => $builder->appendShowPage($entityKey, $instanceId));
    }

    private function buildCurrentPageUrl(BreadcrumbBuilder $builder): string
    {
        return url(
            sprintf(
                '/%s/%s/%s',
                sharp()->config()->get('custom_url_segment'),
                sharp()->context()->globalFilterUrlSegmentValue(),
                $builder->generateUri()
            )
        );
    }

    private function resolveEntityKey(string $entityClassNameOrKey): string
    {
        return app(SharpEntityManager::class)->entityKeyFor($entityClassNameOrKey);
    }
}
