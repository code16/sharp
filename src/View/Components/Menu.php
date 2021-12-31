<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\Utils\Menu\SharpMenuItem;
use Code16\Sharp\Utils\Menu\SharpMenuSection;
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
        $this->title = config("sharp.name", "Sharp");
        $this->username = sharp_user()->{config("sharp.auth.display_attribute", "name")};
        $this->currentEntity = currentSharpRequest()->breadcrumb()->first()->key ?? null;
        $this->hasGlobalFilters = sizeof(config('sharp.global_filters') ?? []) > 0;
        $this->items = $this->getItems();
    }
    
    public function getItems(): Collection
    {
        $sharpMenu = config("sharp.menu", []);
        
        if(is_array($sharpMenu)) {
            // Menu is defined in the config file (Sharp 6 way, legacy)
            $items = collect($sharpMenu)
                ->map(function(array $itemConfig) {
                    if($itemConfig['entities'] ?? false) {
                        return tap(
                            new SharpMenuSection($itemConfig['label'] ?? null),
                            function(SharpMenuSection $section) use($itemConfig) {
                                collect($itemConfig['entities'])
                                    ->each(function(array $entityConfig) use (&$section) {
                                        $section->addEntityLink(
                                            $entityConfig['entity'] ?? ($entityConfig['dashboard'] ?? null),
                                            $entityConfig['label'] ?? null,
                                            $entityConfig['icon'] ?? null,
                                        );
                                    });
                            }
                        );
                    }
                    
                    return new SharpMenuItem(
                        $itemConfig['entity'] ?? ($itemConfig['dashboard'] ?? null),
                        $itemConfig['label'] ?? null,
                        $itemConfig['icon'] ?? null,
                    );
                });
        } else {
            // Menu is built in a class (Sharp 7 way)
            $items = app($sharpMenu)->build()->items();
        }

        return $items
            ->map(function(SharpMenuItem $item) {
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
}
