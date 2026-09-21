<?php

namespace Code16\Sharp\Auth\Passkeys\Commands;

use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class UpdatePasskeyNameCommand extends InstanceCommand
{
    public function label(): string
    {
        return trans('sharp::auth.passkeys.list.commands.rename.command_label');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields->addField(
            SharpFormTextField::make('name')
                ->setLabel(trans('sharp::auth.passkeys.list.commands.rename.name_field_label'))
        );
    }

    protected function initialData(mixed $instanceId): array
    {
        return [
            'name' => auth()->user()->passkeys()->findOrFail($instanceId)->name,
        ];
    }

    public function execute(mixed $instanceId, array $data = []): array
    {
        $this->validate($data, [
            'name' => 'required',
        ]);

        auth()->user()->passkeys()->findOrFail($instanceId)->update([
            'name' => $data['name'],
        ]);

        return $this->refresh($instanceId);
    }

    public function authorizeFor(mixed $instanceId): bool
    {
        return auth()->user()->passkeys()->whereKey($instanceId)->exists();
    }
}
