<?php

namespace Code16\Sharp\Http\Composers\Utils;

abstract class MenuItem
{
    /** @var string */
    public $label;

    /**
     * @param array $config
     * @return MenuItemCategory|MenuItemDashboard|MenuItemEntity|MenuItemUrl|null
     */
    public static function parse(array $config)
    {
        $menuItem = null;

        if(isset($config['entities'])) {
            $menuItem = new MenuItemCategory($config);

        } elseif(isset($config['entity'])) {
            $menuItem = new MenuItemEntity($config);

        } elseif(isset($config['url'])) {
            $menuItem = new MenuItemUrl($config);

        } elseif(isset($config['dashboard'])) {
            $menuItem = new MenuItemDashboard($config);
        }

        return $menuItem && $menuItem->isValid() ? $menuItem : null;
    }

    /** @return bool */
    public function isMenuItemCategory()
    {
        return false;
    }

    /** @return bool */
    public function isMenuItemEntity()
    {
        return false;
    }

    /** @return bool */
    public function isMenuItemUrl()
    {
        return false;
    }

    /** @return bool */
    public function isMenuItemDashboard()
    {
        return false;
    }

    /** @return bool */
    abstract public function isValid();
}

class MenuItemCategory extends MenuItem
{
    /** @var string */
    public $type = "category";

    /** @var array */
    public $entities = [];

    public function __construct(array $category)
    {
        $this->label = $category["label"] ?? "Unnamed category";

        foreach ((array)($category["entities"] ?? []) as $entityConfig) {
            if($menuEntity = static::parse($entityConfig)) {
                $this->entities[] = $menuEntity;
            }
        }
    }

    /** @return bool */
    public function isValid()
    {
        return count($this->entities) != 0;
    }

    /** @return bool */
    public function isMenuItemCategory()
    {
        return true;
    }
}

class MenuItemEntity extends MenuItem
{
    /** @var string */
    public $key;

    /** @var string */
    public $icon;

    /** @var string */
    public $type = "entity";

    /** @var string */
    public $url;

    public function __construct(array $config)
    {
        if (!sharp_has_ability("entity", $config['entity'])) {
            return;
        }

        $this->key = $config['entity'];
        $this->label = $config["label"] ?? "Unnamed entity";
        $this->icon = $config["icon"] ?? null;
        $this->url = route('code16.sharp.list', $this->key);
    }

    /** @return bool */
    public function isValid()
    {
        return !is_null($this->key);
    }

    /** @return bool */
    public function isMenuItemEntity()
    {
        return true;
    }
}

class MenuItemUrl extends MenuItem
{
    /** @var string */
    public $icon;

    /** @var string */
    public $url;

    /** @var string */
    public $key;

    /** @var string */
    public $type = "url";

    public function __construct(array $config)
    {
        $this->label = $config["label"] ?? "Unlabelled link";
        $this->icon = $config["icon"] ?? null;
        $this->url = $config["url"];
        $this->key = uniqid();
    }

    /** @return bool */
    public function isValid()
    {
        return true;
    }

    /** @return bool */
    public function isMenuItemUrl()
    {
        return true;
    }
}

class MenuItemDashboard extends MenuItem
{
    /** @var string */
    public $key;

    /** @var string */
    public $icon;

    /** @var string */
    public $type = "dashboard";

    /** @var string */
    public $url;

    public function __construct(array $config)
    {
        if (!sharp_has_ability("view", $config['dashboard'])) {
            return;
        }

        $this->key = $config['dashboard'];
        $this->label = $config["label"] ?? "Unnamed dashboard";
        $this->icon = $config["icon"] ?? null;
        $this->url = route('code16.sharp.dashboard', $this->key);
    }

    /** @return bool */
    public function isValid()
    {
        return !is_null($this->key);
    }

    /** @return bool */
    public function isMenuItemDashboard()
    {
        return true;
    }
}