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
     * @return string
     */
    public abstract function type(): string;

    /**
     * @return string
     */
    public abstract function label(): string;
}