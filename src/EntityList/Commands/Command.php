<?php

namespace Code16\Sharp\EntityList\Commands;

abstract class Command
{

    /**
     * @return string
     */
    public abstract function type(): string;

    /**
     * @return string
     */
    public abstract function label(): string;
}