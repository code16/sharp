<?php

namespace Code16\Sharp\View\Components;

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
        return collect(config("sharp.menu", []))
            ->map(function($itemConfig) {
                return MenuItem::parse($itemConfig);
            })
            ->filter()
            ->values();
    }

    public function render()
    {
        return view('sharp::components.menu', [
            'component' => $this,
        ]);
    }
}
