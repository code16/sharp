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

        if(config("sharp.menu")) {
            foreach (config("sharp.menu") as $categoryConfig) {
                $category = new MenuCategory($categoryConfig);

                if(sizeof($category->entities)) {
                    $categories->push($category);
                }
            }
        }

        $sharpMenu = [
            "name" => config("sharp.name", "Sharp"),
            "user" => sharp_user()->{config("sharp.auth.display_attribute", "name")},
            "dashboard" => $this->hasDashboard(),
            "categories" => $categories,
            "currentEntity" => isset($view->entityKey) ? explode(':', $view->entityKey)[0] : null
        ];

        $view->with('sharpMenu', (object)$sharpMenu);
    }

    /**
     * @return bool
     */
    private function hasDashboard()
    {
        return !!config("sharp.dashboard", false);
    }
}

class MenuCategory 
{
    /** @var string */
    public $label;

    /** @var array */
    public $entities;

    public function __construct(array $category)
    {
        $this->label = $category["label"] ?? "Unnamed category";

        foreach((array)$category["entities"] as $entityKey => $entity) {
            if(sharp_has_ability("entity", $entityKey)) {
                $this->entities[] = new MenuEntity($entityKey, $entity);
            }
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