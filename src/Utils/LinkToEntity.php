<?php

namespace Code16\Sharp\Utils;

use Illuminate\Support\Collection;

class LinkToEntity
{

    /** @var string */
    protected $linkText;

    /** @var string */
    protected $entityKey;

    /** @var string */
    protected $searchText;

    /** @var string */
    protected $tooltip = "";

    /** @var */
    protected $instanceId;

    /** @var array */
    protected $filters = [];

    /** @var string */
    protected $sortAttribute;

    /** @var string */
    protected $sortDir;

    /** @var array */
    protected $fullQuerystring = [];

    /**
     * @param string $linkText
     * @param string $entityKey
     */
    public function __construct($linkText, $entityKey)
    {
        $this->linkText = $linkText;
        $this->entityKey = $entityKey;
    }

    /**
     * @param string $searchText
     * @return $this
     */
    public function setSearch($searchText)
    {
        $this->searchText = $searchText;

        return $this;
    }

    /**
     * @param $instanceId
     * @return $this
     */
    public function setInstanceId($instanceId)
    {
        $this->instanceId = $instanceId;

        return $this;
    }

    /**
     * @param string $tooltip
     * @return $this
     */
    public function setTooltip($tooltip)
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function addFilter($name, $value)
    {
        $this->filters[$name] = $value;

        return $this;
    }

    /**
     * @param string $attribute
     * @param string $dir
     * @return $this
     */
    public function setSort($attribute, $dir = 'asc')
    {
        $this->sortAttribute = $attribute;
        $this->sortDir = $dir;

        return $this;
    }

    /**
     * @param array $querystring
     * @return $this
     */
    public function setFullQuerystring($querystring)
    {
        $this->fullQuerystring = $querystring;

        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        return sprintf(
            '<a href="%s" title="%s">%s</a>',
            $this->renderAsUrl(), $this->tooltip, $this->linkText
        );
    }

    /**
     * @return string
     */
    public function renderAsUrl()
    {
        if($this->instanceId) {
            return route("code16.sharp.edit",
                array_merge([
                    'entityKey' => $this->entityKey,
                    'instanceId' => $this->instanceId
                ], $this->generateQuerystring()));
        }

        return route("code16.sharp.list",
            array_merge([
                'entityKey' => $this->entityKey
            ], $this->generateQuerystring()));
    }

    /**
     * @return array
     */
    protected function generateQuerystring()
    {
        if(count($this->fullQuerystring)) {
            return $this->fullQuerystring;
        }

        return collect()
            ->when($this->searchText, function(Collection $qs) {
                return $qs->put('search', $this->searchText);
            })
            ->when(count($this->filters), function(Collection $qs) {
                collect($this->filters)->each(function($value, $name) use($qs) {
                    $qs->put("filter_$name", $value);
                });

                return $qs;
            })
            ->when($this->sortAttribute, function(Collection $qs) {
                $qs->put('sort', $this->sortAttribute);
                $qs->put('dir', $this->sortDir);

                return $qs;
            })
            ->all();
    }
}