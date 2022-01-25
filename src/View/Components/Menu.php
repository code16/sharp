<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\Utils\Menu\SharpMenuItem;
use Code16\Sharp\Utils\Menu\SharpMenuItemLink;
use Code16\Sharp\Utils\Menu\SharpMenuItemSection;
use Code16\Sharp\Utils\Menu\SharpMenuItemSeparator;
use Code16\Sharp\View\Components\Utils\MenuItem;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Menu extends Component
{
    public string $title;
    public ?string $username;
    public ?string $currentEntity;
    public bool $hasGlobalFilters;
    public Collection $items;

    public function __construct()
    {
        $this->title = config('sharp.name', 'Sharp');
        $this->username = sharp_user()->{config('sharp.auth.display_attribute', 'name')};
        $this->currentEntity = currentSharpRequest()->breadcrumb()->first()->key ?? null;
        $this->hasGlobalFilters = sizeof(config('sharp.global_filters') ?? []) > 0;
        $this->items = $this->getItems();
    }

    public function getItems(): Collection
    {
        $sharpMenu = config('sharp.menu', []) ?? [];

        $items = is_array($sharpMenu)
            ? $this->getItemFromLegacyConfig($sharpMenu)
            : app($sharpMenu)->build()->items();

        return $items
            ->map(function (SharpMenuItem $item) {
                return MenuItem::buildFromItemClass($item);
            })
            ->filter()
            ->values();
    }

    public function render()
    {
        return view('sharp::components.menu', [
            'self' => $this,
        ]);
    }

    public function getItemFromLegacyConfig(array $sharpMenuConfig): Collection
    {
        // Sanitize legacy Sharp 6 config format to new Sharp 7 format
        return collect($sharpMenuConfig)
            ->map(function (array $itemConfig) {
                if ($itemConfig['entities'] ?? false) {
                    return tap(
                        new SharpMenuItemSection($itemConfig['label'] ?? null),
                        function (SharpMenuItemSection $section) use ($itemConfig) {
                            collect($itemConfig['entities'])
                                ->each(function (array $entityConfig) use (&$section) {
                                    if ($entityConfig['separator'] ?? false) {
                                        $section->addSeparator($entityConfig['label']);
                                    } else {
                                        $section->addEntityLink(
                                            $entityConfig['entity'] ?? ($entityConfig['dashboard'] ?? null),
                                            $entityConfig['label'] ?? null,
                                            $entityConfig['icon'] ?? null,
                                        );
                                    }
                                });
                        },
                    );
                }

                if ($itemConfig['separator'] ?? false) {
                    return new SharpMenuItemSeparator($itemConfig['label']);
                }

                $item = new SharpMenuItemLink(
                    $itemConfig['label'] ?? null,
                    $itemConfig['icon'] ?? null,
                );
                if ($itemConfig['url'] ?? false) {
                    $item->setUrl($itemConfig['url']);
                } else {
                    $item->setEntity($itemConfig['entity'] ?? ($itemConfig['dashboard'] ?? null));
                }

                return $item;
            });
    }
}
