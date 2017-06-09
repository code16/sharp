<?php

namespace Code16\Sharp\Form\Eloquent\Request;

use Illuminate\Support\Collection;

class UpdateRequestData
{

    /**
     * @var Collection
     */
    protected $items;

    public function __construct()
    {
        $this->items = new Collection;
    }

    /**
     * @param string $attribute
     * @return UpdateRequestDataItem
     */
    public function add($attribute)
    {
        $item = new UpdateRequestDataItem($attribute);
        $this->items[] = $item;

        return $item;
    }

    /**
     * @return Collection
     */
    public function items()
    {
        return $this->items;
    }

    /**
     * @param $attribute
     * @return UpdateRequestDataItem
     */
    public function findItem($attribute)
    {
        return $this->items->filter(function($item) use($attribute) {
            return $item->attribute() == $attribute;
        })->first();
    }
}