<?php

namespace App\Sharp\Authors\Commands;

use App\Models\User;
use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Support\Str;

class InviteUserCommand extends EntityCommand
{
    public function label(): ?string
    {
        return 'Invite new author...';
    }

    public function buildCommandConfig(): void
    {
        $this->configureFormModalTitle('Invite a new user as author')
            ->configureFormModalDescription('Provide the email address of the new author, and an invitation will be sent to him (not true, we won’t send anything).')
            ->configureFormModalButtonLabel('Send invitation')
            ->configureFormModalSubmitAndReopenButton('Send & send another');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('email')
                    ->setLabel('Email'),
            )
            ->addField(
                SharpFormTextField::make('name')
                    ->setLabel('Name'),
            );
    }

    public function execute(array $data = []): array
    {
        $this->validate($data, [
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'name' => ['required', 'string', 'max:100'],
        ]);

        User::create([
            'email' => $data['email'],
            'name' => $data['name'],
            'password' => bcrypt(Str::random()),
            'role' => 'editor',
        ]);

        // Here we send an invitation, or something

        return $this->info('Invitation sent!', reload: true);
    }
}
