<?php

namespace App\Sharp\Authors\Commands;

use Code16\Sharp\EntityList\Commands\InstanceCommand;

class VisitFacebookProfileCommand extends InstanceCommand
{
    public function label(): string
    {
        return "Visit author's facebook profile";
    }

    public function buildCommandConfig(): void
    {
        $this->configureDescription('You will leave sharp');
    }

    public function execute(mixed $instanceId, array $data = []): array
    {
        return $this->link('https://facebook.com');
    }
}
