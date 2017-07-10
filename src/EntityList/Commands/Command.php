<?php

namespace Code16\Sharp\EntityList\Commands;

abstract class Command
{

    /**
     * @param string $message
     * @return array
     */
    protected function info(string $message)
    {
        return [
            "action" => "info",
            "message" => $message
        ];
    }

    /**
     * @return array
     */
    protected function reload()
    {
        return [
            "action" => "reload"
        ];
    }

    /**
     * @param string $bladeView
     * @param array $params
     * @return array
     */
    protected function view(string $bladeView, array $params = [])
    {
        return [
            "action" => "view",
            "html" => view($bladeView, $params)->render()
        ];
    }


    /**
     * @return string
     */
    public abstract function type(): string;

    /**
     * @return string
     */
    public abstract function label(): string;
}