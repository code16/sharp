<?php

namespace Code16\Sharp\EntityList\Commands;

abstract class EntityCommand extends Command
{

    /**
     * @return string
     */
    public function type(): string
    {
        return "entity";
    }
}