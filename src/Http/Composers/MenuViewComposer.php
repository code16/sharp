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
        $menuItems = new Collection;

        foreach (config("sharp.menu", []) as $menuItemConfig) {
            if (isset($menuItemConfig['entities'])) {
                $menuItem = new MenuCategory($menuItemConfig);

            } else {
                $menuItem = new MenuEntity(
                    $menuItemConfig['entity'] ?? uniqid(),
                    $menuItemConfig,
                    isset($menuItemConfig['url']) ? "url" : "entity"
                );
            }

            if($menuItem->isValid()) {
                $menuItems->push($menuItem);
            }
        }

        $sharpMenu = [
            "name" => config("sharp.name", "Sharp"),
            "user" => sharp_user()->{config("sharp.auth.display_attribute", "name")},
            "dashboard" => $this->hasDashboard(),
            "menuItems" => $menuItems,
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

    /** @var string */
    public $type;

    /** @var array */
    public $entities = [];

    public function __construct(array $category)
    {
        $this->type = "category";
        $this->label = $category["label"] ?? "Unnamed category";

        foreach ((array)($category["entities"] ?? []) as $entityKey => $entity) {
            // Allow $entityKey to be an array key (legacy) or the value of the entity attribute
            $entityKey = $entity["entity"] ?? $entityKey;

            if (sharp_has_ability("entity", $entityKey)) {
                $this->entities[] = new MenuEntity($entityKey, $entity);
            }
        }
    }

    /** @return bool */
    public function isValid()
    {
        return count($this->entities) != 0;
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

    /** @var string */
    public $url;

    /** @var string */
    public $type;

    public function __construct(string $key, array $entity, string $type = "entity")
    {
        if (!sharp_has_ability("entity", $key)) {
            return;
        }

        $this->key = $key;
        $this->type = $type;
        $this->label = $entity["label"] ?? "Unnamed entity";
        $this->icon = $entity["icon"] ?? null;
        $this->url = $entity["url"] ?? null;
    }

    /** @return bool */
    public function isValid()
    {
        return !is_null($this->key);
    }
}