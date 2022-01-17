<?php

namespace App\Sharp\Commands;

use App\Spaceship;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Support\Arr;

class SpaceshipSendMessage extends InstanceCommand
{
    public function label(): string
    {
        return 'Send a text message...';
    }

    public function buildCommandConfig(): void
    {
        $this
            ->configureFormModalTitle('Send a text message')
            ->configureDescription('Will pretend to send a message and increment message count.');
    }

    public function execute($instanceId, array $data = []): array
    {
        $this->validate($data, [
            'message' => 'required',
        ]);

        if ($data['message'] == 'error') {
            throw new SharpApplicativeException("Message can't be «error»");
        }

        Spaceship::where('id', $instanceId)
            ->increment('messages_sent_count');

        return $this->refresh($instanceId);
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextareaField::make('message')
                    ->setLabel('Message')
            )
            ->addField(
                SharpFormCheckField::make('now', 'Send right now?')
                    ->setHelpMessage('Otherwise it will be sent next night.')
            )
            ->addField(
                SharpFormAutocompleteField::make('level', 'local')
                    ->setListItemInlineTemplate('{{label}}')
                    ->setResultItemInlineTemplate('{{label}}')
                    ->setLocalValues([
                        'l' => 'Low',
                        'm' => 'Medium',
                        'h' => 'High',
                    ])
                    ->setLabel('Level')
            );
    }

    protected function initialData(mixed $instanceId): array
    {
        return $this
            ->setCustomTransformer('message', function ($value, Spaceship $instance) {
                return sprintf('%s, message #%s', $instance->name, $instance->messages_sent_count);
            })
            ->setCustomTransformer('level', function ($value, Spaceship $instance) {
                return Arr::random(['l', 'm', 'h', null]);
            })
            ->transform(
                Spaceship::findOrFail($instanceId)
            );
    }

    public function authorizeFor(mixed $instanceId): bool
    {
        return $instanceId % 2 == 0 && $instanceId > 10;
    }
}
