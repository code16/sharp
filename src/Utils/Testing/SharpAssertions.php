<?php

namespace Code16\Sharp\Utils\Testing;

use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;

trait SharpAssertions
{
    private ?array $currentBreadcrumb = null;

    /**
     * This function must be called before using Sharp's assertions
     * (in the setUp() for instance).
     */
    public function initSharpAssertions()
    {
        TestResponse::macro('assertSharpHasAuthorization', function ($authorization) {
            return $this->assertJson(
                ['authorizations' => [$authorization => true]],
            );
        });

        TestResponse::macro('assertSharpHasNotAuthorization', function ($authorization) {
            return $this->assertJson(
                ['authorizations' => [$authorization => false]],
            );
        });

        TestResponse::macro('assertSharpFormHasFieldOfType', function ($name, $formFieldClassName) {
            $type = $formFieldClassName::FIELD_TYPE;

            $this->assertJson(
                ['fields' => [$name => ['key' => $name, 'type' => $type]]],
            );

            $this->assertSharpFormHasFields($name);

            return $this;
        });

        TestResponse::macro('assertSharpFormHasFields', function ($names) {
            foreach ((array) $names as $name) {
                $this->assertJson(
                    ['fields' => [$name => ['key' => $name]]],
                );

                $found = false;
                foreach ($this->decodeResponseJson()['layout']['tabs'] as $tab) {
                    foreach ($tab['columns'] as $column) {
                        foreach ($column['fields'] as $fieldset) {
                            foreach ($fieldset as $field) {
                                if (isset($field['legend'])) {
                                    foreach ($field['fields'] as $fieldsetFields) {
                                        foreach ($fieldsetFields as $field) {
                                            if ($field['key'] == $name) {
                                                $found = true;
                                                break 6;
                                            }
                                        }
                                    }
                                } elseif ($field['key'] == $name) {
                                    $found = true;
                                    break 4;
                                }
                            }
                        }
                    }
                }

                if (! $found) {
                    PHPUnit::fail("The field [$name] was not found on the layout part.");
                }
            }

            return $this;
        });

        TestResponse::macro('assertSharpFormDataEquals', function ($name, $value) {
            return $this->assertJson(
                ['data' => [$name => $value]],
            );
        });
    }

    public function withSharpCurrentBreadcrumb(array $breadcrumb): self
    {
        $this->currentBreadcrumb = $breadcrumb;

        return $this;
    }

    public function getSharpForm(string $entityKey, $instanceId = null)
    {
        return $this
            ->withHeader(
                'referer',
                $this->buildRefererUrl([
                    ['list', $entityKey],
                    ['form', $entityKey, $instanceId],
                ]),
            )
            ->getJson(
                $instanceId
                    ? route('code16.sharp.api.form.edit', [$entityKey, $instanceId])
                    : route('code16.sharp.api.form.create', $entityKey),
            );
    }

    public function deleteSharpForm(string $entityKey, $instanceId)
    {
        return $this
            ->withHeader(
                'referer',
                $this->buildRefererUrl([
                    ['list', $entityKey],
                    ['form', $entityKey, $instanceId],
                ]),
            )
            ->deleteJson(
                route('code16.sharp.api.form.delete', [$entityKey, $instanceId]),
            );
    }

    public function updateSharpForm(string $entityKey, $instanceId, array $data)
    {
        return $this
            ->withHeader(
                'referer',
                $this->buildRefererUrl([
                    ['list', $entityKey],
                    ['form', $entityKey, $instanceId],
                ]),
            )
            ->postJson(
                route('code16.sharp.api.form.update', [$entityKey, $instanceId]),
                $data,
            );
    }

    public function storeSharpForm(string $entityKey, array $data)
    {
        return $this
            ->withHeader(
                'referer',
                $this->buildRefererUrl([
                    ['list', $entityKey],
                    ['form', $entityKey],
                ]),
            )
            ->postJson(
                route('code16.sharp.api.form.store', $entityKey),
                $data,
            );
    }

    public function callSharpInstanceCommandFromList(string $entityKey, $instanceId, string $commandKeyOrClassName, array $data = [], ?string $commandStep = null)
    {
        $commandKey = class_exists($commandKeyOrClassName)
            ? class_basename($commandKeyOrClassName)
            : $commandKeyOrClassName;

        return $this
            ->withHeader(
                'referer',
                $this->buildRefererUrl([
                    ['list', $entityKey],
                ]),
            )
            ->postJson(
                route(
                    'code16.sharp.api.list.command.instance',
                    compact('entityKey', 'instanceId', 'commandKey'),
                ),
                ['data' => $data, 'command_step' => $commandStep],
            );
    }

    public function callSharpInstanceCommandFromShow(string $entityKey, $instanceId, string $commandKeyOrClassName, array $data = [], ?string $commandStep = null)
    {
        $commandKey = class_exists($commandKeyOrClassName)
            ? class_basename($commandKeyOrClassName)
            : $commandKeyOrClassName;

        return $this
            ->withHeader(
                'referer',
                $this->buildRefererUrl([
                    ['list', $entityKey],
                    ['show', $entityKey, $instanceId],
                ]),
            )
            ->postJson(
                route(
                    'code16.sharp.api.show.command.instance',
                    compact('entityKey', 'instanceId', 'commandKey'),
                ),
                ['data' => $data, 'command_step' => $commandStep],
            );
    }

    public function callSharpEntityCommandFromList(string $entityKey, string $commandKeyOrClassName, array $data = [], ?string $commandStep = null)
    {
        $commandKey = class_exists($commandKeyOrClassName)
            ? class_basename($commandKeyOrClassName)
            : $commandKeyOrClassName;

        return $this
            ->withHeader(
                'referer',
                $this->buildRefererUrl([
                    ['list', $entityKey],
                ]),
            )
            ->postJson(
                route('code16.sharp.api.list.command.entity', compact('entityKey', 'commandKey')),
                ['data' => $data, 'command_step' => $commandStep],
            );
    }

    public function loginAsSharpUser($user): self
    {
        return $this->actingAs($user, config('sharp.auth.guard', config('auth.defaults.guard')));
    }

    protected function buildRefererUrl(array $segments): string
    {
        $segments = $this->currentBreadcrumb ?: $segments;
        $this->currentBreadcrumb = null;

        $uri = collect($segments)
            ->map(function (array $segment) {
                if (count($segment) === 2) {
                    return sprintf('s-%s/%s', $segment[0], $segment[1]);
                } elseif (count($segment) === 3) {
                    return sprintf('s-%s/%s/%s', $segment[0], $segment[1], $segment[2]);
                }

                return null;
            })
            ->filter()
            ->implode('/');

        return url(sprintf('/%s/%s', sharp_base_url_segment(), $uri));
    }

    /**
     * @deprecated use callSharpInstanceCommandFromList or callSharpInstanceCommandFromShow
     */
    protected function callInstanceCommand(string $entityKey, $instanceId, string $commandKey, array $data = [])
    {
        return $this->callSharpInstanceCommandFromList($entityKey, $instanceId, $commandKey, $data);
    }

    /**
     * @deprecated use callSharpEntityCommandFromList
     */
    public function callEntityCommand(string $entityKey, string $commandKey, array $data = [])
    {
        return $this->callSharpInstanceCommandFromList($entityKey, $instanceId, $commandKey, $data);
    }
}
