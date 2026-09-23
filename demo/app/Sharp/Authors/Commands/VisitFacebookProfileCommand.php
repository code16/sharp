<?php

namespace App\Sharp\Authors\Commands;

use App\Models\User;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\EntityList\Commands\Returns\CommandReturn;

class VisitFacebookProfileCommand extends InstanceCommand
{
    public function label(): string
    {
        return 'Visit author’s facebook profile';
    }

    public function buildCommandConfig(): void
    {
        $this->configureDescription('You will leave Sharp')
            ->configureIcon('lucide-facebook');
    }

    public function execute(mixed $instanceId, array $data = []): CommandReturn
    {
        return $this->link('https://facebook.com')->inNewTab();
    }

    public function authorizeFor(mixed $instanceId): bool
    {
        return sharp()->context()
            ->findListInstance($instanceId, fn ($id) => User::find($id))
            ->hasVerifiedEmail();
    }
}
