<?php

namespace Code16\Sharp\Auth\Passkeys\Commands;

use Code16\Sharp\Auth\Passkeys\PasskeyManager;
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
            'name' => app(PasskeyManager::class)->model()::findOrFail($instanceId)->name,
        ];
    }

    public function execute(mixed $instanceId, array $data = []): array
    {
        $this->validate($data, [
            'name' => 'required',
        ]);

        app(PasskeyManager::class)->model()::findOrFail($instanceId)->update([
            'name' => $data['name'],
        ]);

        return $this->refresh($instanceId);
    }
}
