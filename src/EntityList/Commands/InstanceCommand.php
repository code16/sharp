<?php

namespace Code16\Sharp\EntityList\Commands;

abstract class InstanceCommand extends Command
{

    /**
     * @return string
     */
    public function type(): string
    {
        return "instance";
    }
}