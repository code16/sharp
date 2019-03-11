<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\EntityListQueryParams;

class SpaceshipSynchronize extends EntityCommand
{

    /**
     * @return string
     */
    public function label(): string
    {
        return "Synchronize the gamma-spectrum";
    }

    public function description(): string
    {
        return "Let's be honest: this command is a fraud. It's just an empty command for test purpose.";
    }

    /**
     * @param EntityListQueryParams $params
     * @param array $data
     * @return array
     */
    public function execute(EntityListQueryParams $params, array $data=[]): array
    {
        sleep(2);
        return $this->info("Gamma spectrum synchronized!");
    }

    public function confirmationText()
    {
        return "Sure, really?";
    }

    public function authorize():bool
    {
        return sharp_user()->hasGroup("boss");
    }
}