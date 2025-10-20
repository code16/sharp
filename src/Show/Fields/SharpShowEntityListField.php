<?php

namespace Code16\Sharp\Show\Fields;

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Filters\Filter;
use Code16\Sharp\Utils\Entities\SharpEntityManager;

class SharpShowEntityListField extends SharpShowField
{
    const FIELD_TYPE = 'entityList';

    protected string $entityListKey;
    protected array $hiddenFilters = [];
    protected array $hiddenCommands = [
        'entity' => [],
        'instance' => [],
    ];
    protected bool $showEntityState = true;
    protected bool $showReorderButton = true;
    protected bool $showCreateButton = true;
    protected bool $showSearchField = true;
    protected bool $showCount = false;
    protected ?string $label = null;

    public static function make(string $key, ?string $entityListKey = null): SharpShowEntityListField
    {
        if (class_exists($key)) {
            $entityListKey = app(SharpEntityManager::class)->entityKeyFor($key);
        }

        return tap(
            new static($key, static::FIELD_TYPE),
            fn ($instance) => $instance->entityListKey = $entityListKey ?: $key
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

    public function hideEntityCommand(array|string $commands): self
    {
        foreach ((array) $commands as $command) {
            if (class_exists($command)) {
                $command = app($command)->getCommandKey();
            }

            $this->hiddenCommands['entity'][] = $command;
        }

        return $this;
    }

    public function hideInstanceCommand(array|string $commands): self
    {
        foreach ((array) $commands as $command) {
            if (class_exists($command)) {
                $command = app($command)->getCommandKey();
            }

            $this->hiddenCommands['instance'][] = $command;
        }

        return $this;
    }

    public function showEntityState(bool $showEntityState = true): self
    {
        $this->showEntityState = $showEntityState;

        return $this;
    }

    public function showCreateButton(bool $showCreateButton = true): self
    {
        $this->showCreateButton = $showCreateButton;

        return $this;
    }

    public function showReorderButton(bool $showReorderButton = true): self
    {
        $this->showReorderButton = $showReorderButton;

        return $this;
    }

    public function showSearchField(bool $showSearchField = true): self
    {
        $this->showSearchField = $showSearchField;

        return $this;
    }

    public function showCount(bool $showCount = true): self
    {
        $this->showCount = $showCount;

        return $this;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @deprecated Not used anymore, EEL are shown no matter what.
     */
    public function setShowIfEmpty(bool $showIfEmpty = true): SharpShowField
    {
        return $this;
    }

    /**
     * Create the properties array for the field, using parent::buildArray().
     */
    public function toArray(): array
    {
        return tap(
            parent::buildArray([
                'label' => $this->label,
                'entityListKey' => $this->entityListKey,
                'showEntityState' => $this->showEntityState,
                'showCreateButton' => $this->showCreateButton,
                'showReorderButton' => $this->showReorderButton,
                'showSearchField' => $this->showSearchField,
                'showCount' => $this->showCount,
                'hiddenCommands' => $this->hiddenCommands,
                'hiddenFilters' => count($this->hiddenFilters)
                    ? collect($this->hiddenFilters)
                        ->map(fn ($value) => is_callable($value)
                            ? $value(sharp()->context()->instanceId())
                            : $value
                        )
                        ->all()
                    : null,
                'authorizations' => [
                    'view' => app(SharpAuthorizationManager::class)->isAllowed('entity', $this->entityListKey),
                ],
            ]),
            function (array &$options) {
                $options['endpointUrl'] = route('code16.sharp.api.list', [
                    'entityKey' => $this->entityListKey,
                    'current_page_url' => request()->url(),
                    ...app(SharpEntityManager::class)
                        ->entityFor($this->entityListKey)
                        ->getListOrFail()
                        ->filterContainer()
                        ->getQueryParamsFromFilterValues($options['hiddenFilters'] ?? []),
                ]);
            });
    }

    protected function validationRules(): array
    {
        return [
            'entityListKey' => 'required',
            'showEntityState' => 'required|boolean',
            'showCreateButton' => 'required|boolean',
            'showReorderButton' => 'required|boolean',
            'showCount' => 'required|boolean',
            'hiddenCommands' => 'required|array',
            'hiddenCommands.entity' => 'array',
            'hiddenCommands.instance' => 'array',
            'hiddenFilters' => 'array',
        ];
    }
}
