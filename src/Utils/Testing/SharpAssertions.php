<?php

namespace Code16\Sharp\Utils\Testing;

use Closure;
use Code16\Sharp\Http\Context\SharpBreadcrumb;
use Code16\Sharp\Utils\Links\BreadcrumbBuilder;

trait SharpAssertions
{
    private BreadcrumbBuilder $breadcrumbBuilder;

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

    public function deleteFromSharpShow(string $entityKey, $instanceId)
    {
        return $this
            ->delete(
                route(
                    'code16.sharp.show.delete', [
                        $this->breadcrumbBuilder($entityKey)
                            ->generateUri(),
                        $entityKey,
                        $instanceId,
                    ]
                ),
            );
    }

    public function deleteFromSharpList(string $entityKey, $instanceId)
    {
        return $this
            ->withHeader(
                SharpBreadcrumb::CURRENT_PAGE_URL_HEADER,
                $this->buildCurrentPageUrl(
                    $this->breadcrumbBuilder($entityKey)
                ),
            )
            ->delete(route('code16.sharp.api.list.delete', [$entityKey, $instanceId]));
    }

    public function getSharpForm(string $entityKey, $instanceId = null)
    {
        return $this
            ->get(
                $instanceId
                    ? route(
                        'code16.sharp.form.edit',
                        [
                            $this->breadcrumbBuilder($entityKey)
                                ->generateUri(),
                            $entityKey,
                            $instanceId,
                        ]
                    )
                    : route(
                        'code16.sharp.form.create',
                        [
                            $this->breadcrumbBuilder($entityKey)
                                ->generateUri(),
                            $entityKey,
                        ]
                    ),
            );
    }

    public function getSharpSingleForm(string $entityKey)
    {
        return $this
            ->get(
                route(
                    'code16.sharp.form.edit',
                    [
                        $this->breadcrumbBuilder($entityKey)
                            ->generateUri(),
                        $entityKey,
                    ]
                )
            );
    }

    public function updateSharpForm(string $entityKey, $instanceId, array $data)
    {
        return $this
            ->post(
                route(
                    'code16.sharp.form.update', [
                        $this->breadcrumbBuilder($entityKey)
                            ->generateUri(),
                        $entityKey,
                        $instanceId,
                    ]
                ),
                $data,
            );
    }

    public function updateSharpSingleForm(string $entityKey, array $data)
    {
        return $this
            ->post(
                route(
                    'code16.sharp.form.update', [
                        $this->breadcrumbBuilder($entityKey)
                            ->generateUri(),
                        $entityKey,
                    ]
                ),
                $data,
            );
    }

    public function getSharpShow(string $entityKey, $instanceId)
    {
        return $this
            ->get(
                route(
                    'code16.sharp.show.show',
                    [
                        $this->breadcrumbBuilder($entityKey)
                            ->generateUri(),
                        $entityKey,
                        $instanceId,
                    ]
                ),
            );
    }

    public function storeSharpForm(string $entityKey, array $data)
    {
        return $this
            ->post(
                route(
                    'code16.sharp.form.store',
                    [
                        $this->breadcrumbBuilder($entityKey)
                            ->generateUri(),
                        $entityKey,
                    ]
                ),
                $data,
            );
    }

    public function callSharpInstanceCommandFromList(
        string $entityKey,
        $instanceId,
        string $commandKeyOrClassName,
        array $data = [],
        ?string $commandStep = null
    ) {
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
        string $entityKey,
        $instanceId,
        string $commandKeyOrClassName,
        array $data = [],
        ?string $commandStep = null
    ) {
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
        string $entityKey,
        string $commandKeyOrClassName,
        array $data = [],
        ?string $commandStep = null
    ) {
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
                '/%s/%s',
                sharp()->config()->get('custom_url_segment'),
                $builder->generateUri()
            )
        );
    }
}
