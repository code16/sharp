<?php

namespace Code16\Sharp\Http;

class SharpContext
{
    /**
     * @var string
     */
    protected $page;

    /**
     * @var mixed
     */
    protected $instanceId;

    /**
     * @var string
     */
    protected $action;

    public function setIsForm()
    {
        $this->page = "FORM";
    }

    /**
     * @param $instanceId
     */
    public function setIsUpdate($instanceId)
    {
        $this->setIsForm();
        $this->instanceId = $instanceId;
        $this->action = "UPDATE";
    }

    public function setIsCreation()
    {
        $this->setIsForm();
        $this->action = "CREATION";
    }

    /**
     * @return bool
     */
    public function isForm(): bool
    {
        return $this->page == "FORM";
    }

    /**
     * @return bool
     */
    public function isUpdate(): bool
    {
        return $this->isForm() && $this->action == "UPDATE";
    }

    /**
     * @return bool
     */
    public function isCreation(): bool
    {
        return $this->isForm() && $this->action == "CREATION";
    }

    /**
     * @return mixed|null
     */
    public function instanceId()
    {
        return $this->isUpdate()
            ? $this->instanceId
            : null;
    }

    /**
     * @param string $filterName
     * @return array|string|null
     */
    public function globalFilterFor(string $filterName)
    {
        if(!$handlerClass = config("sharp.global_filters.$filterName")) {
            return null;
        }

        $handler = app($handlerClass);

        if(session()->has("_sharp_retained_global_filter_$filterName")) {
            return session()->get("_sharp_retained_global_filter_$filterName");
        }

        return $handler->defaultValue();
    }
}