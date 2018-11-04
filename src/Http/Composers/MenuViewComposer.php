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

        if(config("sharp.menu")) {
            foreach (config("sharp.menu") as $menuItemConfig) {
                if (isset($menuItemConfig['entities'])) {
                    $menuItem = new MenuCategory($menuItemConfig);
                    $menuItem->type = 'category';
                } else if (isset($menuItemConfig['entity'])) {
                    $menuItem = new MenuEntity($menuItemConfig['entity'], $menuItemConfig);
                    $menuItem->type = 'page';
                } else if(isset($menuItemConfig['url'])) {
                    $menuItem = new MenuEntity('url', $menuItemConfig);
                    $menuItem->type = 'url';
                }
                if(($menuItem->type == 'category' && sizeof($menuItem->entities)) || $menuItem->type == 'page' || $menuItem->type == 'url') {
                    $menuItems->push($menuItem);
                }
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

    /** @var array */
    public $entities = [];

    public function __construct(array $category)
    {
        $this->label = $category["label"] ?? "Unnamed category";

        if (isset($category['entities'])) {
            foreach ((array)$category["entities"] as $entityKey => $entity) {
                if (sharp_has_ability("entity", $entityKey)) {
                    $this->entities[] = new MenuEntity($entityKey, $entity);
                }
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

    /** @var string */
    public $url;

    /** @var LinkTypes */
    public $linkType;

    public function __construct(string $key, array $entity)
    {
        $this->key = $key;
        $this->label = $entity["label"] ?? "Unnamed entity";
        $this->icon = $entity["icon"] ?? null;
        $this->url = $entity["url"] ?? null;
        if (isset($entity['link_type']) && $entity['link_type']) {
            if ($entity['link_type'] == 'form') {
                $this->linkType = LinkTypes::FORM;
            } else {
                $this->linkType = LinkTypes::LIST;
            }
        } else {
            $this->linkType = LinkTypes::LIST;
        }
    }
}

class LinkTypes {
    const LIST = 0;
    const FORM = 1;
}