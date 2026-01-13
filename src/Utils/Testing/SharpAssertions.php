<?php

namespace Code16\Sharp\Utils\Testing;

use Closure;
use Code16\Sharp\Http\Context\SharpBreadcrumb;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Links\BreadcrumbBuilder;
use Code16\Sharp\Utils\Testing\EntityList\PendingEntityList;
use Code16\Sharp\Utils\Testing\Form\PendingForm;
use Code16\Sharp\Utils\Testing\Show\PendingShow;

trait SharpAssertions
{
    use GeneratesCurrentPageUrl;
    use GeneratesGlobalFilterUrl;

    private BreadcrumbBuilder $breadcrumbBuilder;

    public function sharpList(string $entityClassNameOrKey): PendingEntityList
    {
        return new PendingEntityList($this, $entityClassNameOrKey);
    }

    public function sharpShow(string $entityClassNameOrKey, int|string|null $instanceId = null): PendingShow
    {
        return new PendingShow($this, $entityClassNameOrKey, $instanceId);
    }

    public function sharpForm(string $entityClassNameOrKey, int|string|null $instanceId = null): PendingForm
    {
        return new PendingForm($this, $entityClassNameOrKey, $instanceId);
    }

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

    public function deleteFromSharpShow(string $entityClassNameOrKey, mixed $instanceId)
    {
        $this->setGlobalFilterUrlDefault();

        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        return $this
            ->delete(
                route(
                    'code16.sharp.show.delete', [
                        'globalFilter' => $this->globalFilter,
                        'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                        'entityKey' => $entityKey,
                        'instanceId' => $instanceId,
                    ]
                ),
            );
    }

    public function deleteFromSharpList(string $entityClassNameOrKey, mixed $instanceId)
    {
        $this->setGlobalFilterUrlDefault();

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
                    'globalFilter' => $this->globalFilter,
                    'entityKey' => $entityKey,
                    'instanceId' => $instanceId,
                ])
            );
    }

    public function getSharpForm(string $entityClassNameOrKey, mixed $instanceId = null)
    {
        $this->setGlobalFilterUrlDefault();

        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        return $this
            ->get(
                $instanceId
                    ? route(
                        'code16.sharp.form.edit',
                        [
                            'globalFilter' => $this->globalFilter,
                            'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                            'entityKey' => $entityKey,
                            'instanceId' => $instanceId,
                        ]
                    )
                    : route(
                        'code16.sharp.form.create',
                        [
                            'globalFilter' => $this->globalFilter,
                            'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                            'entityKey' => $entityKey,
                        ]
                    ),
            );
    }

    public function getSharpSingleForm(string $entityClassNameOrKey)
    {
        $this->setGlobalFilterUrlDefault();

        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        return $this
            ->get(
                route(
                    'code16.sharp.form.edit',
                    [
                        'globalFilter' => $this->globalFilter,
                        'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                        'entityKey' => $entityKey,
                    ]
                )
            );
    }

    public function updateSharpForm(string $entityClassNameOrKey, $instanceId, array $data)
    {
        $this->setGlobalFilterUrlDefault();

        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        return $this
            ->post(
                route(
                    'code16.sharp.form.update', [
                        'globalFilter' => $this->globalFilter,
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
        $this->setGlobalFilterUrlDefault();

        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        return $this
            ->post(
                route(
                    'code16.sharp.form.update', [
                        'globalFilter' => $this->globalFilter,
                        'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                        'entityKey' => $entityKey,
                    ]
                ),
                $data,
            );
    }

    public function getSharpShow(string $entityClassNameOrKey, $instanceId)
    {
        $this->setGlobalFilterUrlDefault();

        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        return $this
            ->get(
                route(
                    'code16.sharp.show.show',
                    [
                        'globalFilter' => $this->globalFilter,
                        'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                        'entityKey' => $entityKey,
                        'instanceId' => $instanceId,
                    ]
                ),
            );
    }

    public function storeSharpForm(string $entityClassNameOrKey, array $data)
    {
        $this->setGlobalFilterUrlDefault();

        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        return $this
            ->post(
                route(
                    'code16.sharp.form.store',
                    [
                        'globalFilter' => $this->globalFilter,
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
        $this->setGlobalFilterUrlDefault();

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
        $this->setGlobalFilterUrlDefault();

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
        $this->setGlobalFilterUrlDefault();

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

    private function resolveEntityKey(string $entityClassNameOrKey): string
    {
        return app(SharpEntityManager::class)->entityKeyFor($entityClassNameOrKey);
    }
}
