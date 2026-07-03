<?php

namespace Code16\Sharp\Utils\Testing;

use Closure;
use Code16\Sharp\Http\Context\SharpBreadcrumb;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Links\BreadcrumbBuilder;
use Code16\Sharp\Utils\Testing\Dashboard\PendingDashboard;
use Code16\Sharp\Utils\Testing\EntityList\PendingEntityList;
use Code16\Sharp\Utils\Testing\Form\PendingForm;
use Code16\Sharp\Utils\Testing\Show\PendingShow;

trait SharpAssertions
{
    use GeneratesCurrentPageUrl;
    use HasGlobalFilters;

    private BreadcrumbBuilder $breadcrumbBuilder;

    public function sharpList(string $entityClassNameOrKey): PendingEntityList
    {
        $this->setGlobalFilterUrlDefault();

        return new PendingEntityList($this, $entityClassNameOrKey);
    }

    public function sharpShow(string $entityClassNameOrKey, int|string|null $instanceId = null): PendingShow
    {
        $this->setGlobalFilterUrlDefault();

        return new PendingShow($this, $entityClassNameOrKey, $instanceId);
    }

    public function sharpForm(string $entityClassNameOrKey, int|string|null $instanceId = null): PendingForm
    {
        $this->setGlobalFilterUrlDefault();

        return new PendingForm($this, $entityClassNameOrKey, $instanceId);
    }

    public function sharpDashboard(string $entityClassNameOrKey): PendingDashboard
    {
        $this->setGlobalFilterUrlDefault();

        return new PendingDashboard($this, $entityClassNameOrKey);
    }

    /**
     * @deprecated Chain $this->sharpList()->sharpShow()->sharpForm() instead
     *
     * @param  (Closure(BreadcrumbBuilder): BreadcrumbBuilder)  $callback
     * @return $this
     */
    public function withSharpBreadcrumb(Closure $callback): static
    {
        $this->breadcrumbBuilder = $callback(new BreadcrumbBuilder());

        return $this;
    }

    /**
     * @deprecated Use $this->sharpShow()->delete() instead
     */
    public function deleteFromSharpShow(string $entityClassNameOrKey, mixed $instanceId)
    {
        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        $this->setGlobalFilterUrlDefault();

        return $this
            ->delete(
                route(
                    'code16.sharp.show.delete', [
                        'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                        'entityKey' => $entityKey,
                        'instanceId' => $instanceId,
                    ]
                ),
            );
    }

    /**
     * @deprecated Use $this->sharpList()->delete() instead
     */
    public function deleteFromSharpList(string $entityClassNameOrKey, mixed $instanceId)
    {
        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        $this->setGlobalFilterUrlDefault();

        return $this
            ->withHeader(
                SharpBreadcrumb::CURRENT_PAGE_URL_HEADER,
                $this->buildCurrentPageUrl(
                    $this->breadcrumbBuilder($entityKey)->generateUri()
                ),
            )
            ->delete(
                route('code16.sharp.api.list.delete', [
                    'entityKey' => $entityKey,
                    'instanceId' => $instanceId,
                ])
            );
    }

    /**
     * @deprecated Use $this->sharpForm()->get() instead
     */
    public function getSharpForm(string $entityClassNameOrKey, mixed $instanceId = null)
    {
        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        $this->setGlobalFilterUrlDefault();

        return $this
            ->get(
                $instanceId
                    ? route(
                        'code16.sharp.form.edit',
                        [
                            'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                            'entityKey' => $entityKey,
                            'instanceId' => $instanceId,
                        ]
                    )
                    : route(
                        'code16.sharp.form.create',
                        [
                            'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                            'entityKey' => $entityKey,
                        ]
                    ),
            );
    }

    /**
     * @deprecated Use $this->sharpForm()->get() instead
     */
    public function getSharpSingleForm(string $entityClassNameOrKey)
    {
        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        $this->setGlobalFilterUrlDefault();

        return $this
            ->get(
                route(
                    'code16.sharp.form.edit',
                    [
                        'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                        'entityKey' => $entityKey,
                    ]
                )
            );
    }

    /**
     * @deprecated Use $this->sharpForm()->update() instead
     */
    public function updateSharpForm(string $entityClassNameOrKey, $instanceId, array $data)
    {
        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        $this->setGlobalFilterUrlDefault();

        return $this
            ->post(
                route(
                    'code16.sharp.form.update', [
                        'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                        'entityKey' => $entityKey,
                        'instanceId' => $instanceId,
                    ]
                ),
                $data,
            );
    }

    /**
     * @deprecated Use $this->sharpForm()->update() instead
     */
    public function updateSharpSingleForm(string $entityClassNameOrKey, array $data)
    {
        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        $this->setGlobalFilterUrlDefault();

        return $this
            ->post(
                route(
                    'code16.sharp.form.update', [
                        'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                        'entityKey' => $entityKey,
                    ]
                ),
                $data,
            );
    }

    /**
     * @deprecated Use $this->sharpShow()->get() instead
     */
    public function getSharpShow(string $entityClassNameOrKey, $instanceId)
    {
        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        $this->setGlobalFilterUrlDefault();

        return $this
            ->get(
                route(
                    'code16.sharp.show.show',
                    [
                        'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                        'entityKey' => $entityKey,
                        'instanceId' => $instanceId,
                    ]
                ),
            );
    }

    /**
     * @deprecated Use $this->sharpForm()->store() instead
     */
    public function storeSharpForm(string $entityClassNameOrKey, array $data)
    {
        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        $this->setGlobalFilterUrlDefault();

        return $this
            ->post(
                route(
                    'code16.sharp.form.store',
                    [
                        'parentUri' => $this->breadcrumbBuilder($entityKey)->generateUri(),
                        'entityKey' => $entityKey,
                    ]
                ),
                $data,
            );
    }

    /**
     * @deprecated Use $this->sharpList()->instanceCommand()->post()
     */
    public function callSharpInstanceCommandFromList(
        string $entityClassNameOrKey,
        $instanceId,
        string $commandKeyOrClassName,
        array $data = [],
        ?string $commandStep = null
    ) {
        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        $commandKey = class_exists($commandKeyOrClassName)
            ? class_basename($commandKeyOrClassName)
            : $commandKeyOrClassName;

        $this->setGlobalFilterUrlDefault();

        return $this
            ->withHeader(
                SharpBreadcrumb::CURRENT_PAGE_URL_HEADER,
                $this->buildCurrentPageUrl(
                    $this->breadcrumbBuilder($entityKey)->generateUri()
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

    /**
     * @deprecated Use $this->sharpShow()->instanceCommand()->post()
     */
    public function callSharpInstanceCommandFromShow(
        string $entityClassNameOrKey,
        $instanceId,
        string $commandKeyOrClassName,
        array $data = [],
        ?string $commandStep = null
    ) {
        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        $commandKey = class_exists($commandKeyOrClassName)
            ? class_basename($commandKeyOrClassName)
            : $commandKeyOrClassName;

        $this->setGlobalFilterUrlDefault();

        return $this
            ->withHeader(
                SharpBreadcrumb::CURRENT_PAGE_URL_HEADER,
                $this->buildCurrentPageUrl(
                    $this->breadcrumbBuilder($entityKey, $instanceId)->generateUri()
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

    /**
     * @deprecated Use $this->sharpList()->entityCommand()->post()
     */
    public function callSharpEntityCommandFromList(
        string $entityClassNameOrKey,
        string $commandKeyOrClassName,
        array $data = [],
        ?string $commandStep = null
    ) {
        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);

        $commandKey = class_exists($commandKeyOrClassName)
            ? class_basename($commandKeyOrClassName)
            : $commandKeyOrClassName;

        $this->setGlobalFilterUrlDefault();

        return $this
            ->withHeader(
                SharpBreadcrumb::CURRENT_PAGE_URL_HEADER,
                $this->buildCurrentPageUrl($this->breadcrumbBuilder($entityKey)->generateUri())
            )
            ->postJson(
                route('code16.sharp.api.list.command.entity', compact('entityKey', 'commandKey')),
                ['data' => $data, 'command_step' => $commandStep],
            );
    }

    public function loginAsSharpUser($user): static
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
