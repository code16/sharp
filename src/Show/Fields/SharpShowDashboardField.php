<?php

namespace Code16\Sharp\Show\Fields;

use Code16\Sharp\Filters\Filter;
use Code16\Sharp\Utils\Entities\SharpEntityManager;

class SharpShowDashboardField extends SharpShowField
{
    const string FIELD_TYPE = 'dashboard';

    protected string $dashboardKey;
    protected array $hiddenFilters = [];
    protected array $hiddenCommands = [];
    protected ?string $label = null;

    public static function make(string $key, ?string $dashboardKey = null): SharpShowDashboardField
    {
        if (class_exists($key)) {
            $dashboardKey = app(SharpEntityManager::class)->entityKeyFor($key);
        }

        return tap(
            new static($key, static::FIELD_TYPE),
            fn ($instance) => $instance->dashboardKey = $dashboardKey ?: $key
        );
    }

    public function hideFilterWithValue(string $filterFullClassNameOrKey, $value): self
    {
        if (class_exists($filterFullClassNameOrKey)) {
            $key = tap(
                app($filterFullClassNameOrKey), function (Filter $filter) {
                    $filter->buildFilterConfig();
                })
                ->getKey();
        } else {
            $key = $filterFullClassNameOrKey;
        }

        $this->hiddenFilters[$key] = $value;

        return $this;
    }

    public function hideDashboardCommand(array|string $commands): self
    {
        foreach ((array) $commands as $command) {
            if (class_exists($command)) {
                $command = app($command)->getCommandKey();
            }

            $this->hiddenCommands[] = $command;
        }

        return $this;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Create the property array for the field, using parent::buildArray().
     */
    public function toArray(): array
    {
        return tap(
            parent::buildArray([
                'label' => $this->label,
                'dashboardKey' => $this->dashboardKey,
                'hiddenCommands' => $this->hiddenCommands,
                'hiddenFilters' => count($this->hiddenFilters)
                    ? collect($this->hiddenFilters)
                        ->map(fn ($value) => is_callable($value)
                            ? $value(sharp()->context()->instanceId())
                            : $value
                        )
                        ->all()
                    : null,
            ]),
            function (array &$options) {
                $options['endpointUrl'] = route('code16.sharp.api.dashboard', [
                    'entityKey' => $this->dashboardKey,
                    'current_page_url' => request()->url(),
                    ...app(SharpEntityManager::class)
                        ->entityFor($this->dashboardKey)
                        ->getViewOrFail()
                        ->filterContainer()
                        ->getQueryParamsFromFilterValues($options['hiddenFilters'] ?? []),
                ]);
            });
    }

    protected function validationRules(): array
    {
        return [
            'dashboardKey' => 'required',
            'hiddenCommands' => 'array',
            'hiddenFilters' => 'array',
        ];
    }
}
