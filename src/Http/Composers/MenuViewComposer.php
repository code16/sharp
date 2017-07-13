<?php

namespace Code16\Sharp\Http\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class MenuViewComposer
{

    /**
     * Build the menu and bind it to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $categories = new Collection;
        
        foreach(config("sharp.menu") as $categoryKey => $category) {
            $categories->push(new MenuCategory($categoryKey, $category));
        }

        $sharpMenu = [
            "name" => config("sharp.name", "Sharp"),
            "user" => sharp_user()->{config("sharp.auth.display_attribute", "name")},
            "categories" => $categories
        ];

        $view->with('sharpMenu', (object)$sharpMenu);
    }
}

class MenuCategory 
{
    /** @var string */
    public $key;

    /** @var string */
    public $label;

    /** @var array */
    public $entities;

    public function __construct(string $key, array $category)
    {
        $this->key = $key;
        $this->label = $category["label"] ?? "Unnamed category";

        foreach((array)$category["entities"] as $entityKey => $entity) {
            $this->entities[] = new MenuEntity($entityKey, $entity);
        }
    }
}

class MenuEntity
{
    /** @var string */
    public $key;

    /** @var string */
    public $label;

    /** @var string */
    public $icon;

    public function __construct(string $key, array $entity)
    {
        $this->key = $key;
        $this->label = $entity["label"] ?? "Unnamed entity";
        $this->icon = $entity["icon"] ?? null;
    }
}