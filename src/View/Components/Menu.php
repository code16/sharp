<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\Http\Composers\Utils\MenuItem;
use Code16\Sharp\Http\Context\CurrentSharpRequest;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Menu extends Component
{
    
    public string $title;
    
    public ?string $username;
    
    public $currentEntity;
    
    public bool $hasGlobalFilters;
    
    public Collection $items;
    
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->title = config("sharp.name", "Sharp");
        $this->username = sharp_user()->{config("sharp.auth.display_attribute", "name")};
        $this->currentEntity = app(CurrentSharpRequest::class)->breadcrumb()->first()->key ?? null;
        $this->hasGlobalFilters = sizeof(config('sharp.global_filters') ?? []) > 0;
        $this->items = $this->getItems();
    }
    
    public function getItems(): Collection
    {
        $menuItems = new Collection;
    
        foreach (config("sharp.menu", []) as $menuItemConfig) {
            if($menuItem = MenuItem::parse($menuItemConfig)) {
                $menuItems->push($menuItem);
            }
        }
        
        return $menuItems;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('sharp::components.menu', [
            'component' => $this,
        ]);
    }
}
