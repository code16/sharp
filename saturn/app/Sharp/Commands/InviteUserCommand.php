<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\Form\Fields\SharpFormTextField;

class InviteUserCommand extends EntityCommand
{
    public function label(): string
    {
        return 'Invite new user...';
    }

    public function execute(EntityListQueryParams $params, array $data = []): array
    {
        $this->validate(
            $data,
            [
                'email' => 'required|email',
            ],
        );

        return $this->info('Invitation sent!');
    }

    public function buildFormFields(): void
    {
        $this->addField(
            SharpFormTextField::make('email')
                ->setLabel('E-mail address'),
        );
    }
}
