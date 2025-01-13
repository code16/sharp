<?php

namespace Code16\Sharp\Utils\Testing;

use Code16\Sharp\Http\Context\SharpBreadcrumb;

trait SharpAssertions
{
    private ?array $currentBreadcrumb = null;

    public function withSharpCurrentBreadcrumb(...$breadcrumb): self
    {
        $this->currentBreadcrumb = $breadcrumb;

        return $this;
    }

    public function deleteFromSharpShow(string $entityKey, $instanceId)
    {
        return $this
            ->delete(
                route(
                    'code16.sharp.show.delete', [
                        $this->buildCurrentParentUri(['list', $entityKey]),
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
                    ['list', $entityKey],
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
                        [$this->buildCurrentParentUri(['list', $entityKey]), $entityKey, $instanceId]
                    )
                    : route(
                        'code16.sharp.form.create',
                        [$this->buildCurrentParentUri(['list', $entityKey]), $entityKey]
                    ),
            );
    }

    public function getSharpSingleForm(string $entityKey)
    {
        return $this
            ->get(
                route(
                    'code16.sharp.form.edit',
                    [$this->buildCurrentParentUri(['list', $entityKey]), $entityKey]
                )
            );
    }

    public function updateSharpForm(string $entityKey, $instanceId, array $data)
    {
        return $this
            ->post(
                route(
                    'code16.sharp.form.update', [
                        $this->buildCurrentParentUri(['list', $entityKey]),
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
                        $this->buildCurrentParentUri(['list', $entityKey]),
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
                    [$this->buildCurrentParentUri(['list', $entityKey]), $entityKey, $instanceId]
                ),
            );
    }

    public function storeSharpForm(string $entityKey, array $data)
    {
        return $this
            ->post(
                route(
                    'code16.sharp.form.store',
                    [$this->buildCurrentParentUri(['list', $entityKey]), $entityKey]
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
                    ['list', $entityKey],
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
                    ['list', $entityKey],
                    ['show', $entityKey, $instanceId],
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
                    ['list', $entityKey],
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

    private function buildCurrentPageUrl(...$breadcrumbItems): string
    {
        return url(
            sprintf(
                '/%s/%s',
                sharp()->config()->get('custom_url_segment'),
                $this->buildCurrentParentUri(...$breadcrumbItems)
            )
        );
    }

    private function buildCurrentParentUri(...$breadcrumbItems): string
    {
        return collect($this->currentBreadcrumb ?: $breadcrumbItems)
            ->map(fn (array $segment) => match (count($segment)) {
                2 => sprintf('s-%s/%s', $segment[0], $segment[1]),
                3 => sprintf('s-%s/%s/%s', $segment[0], $segment[1], $segment[2]),
                default => null,
            })
            ->whereNotNull()
            ->implode('/');
    }
}
