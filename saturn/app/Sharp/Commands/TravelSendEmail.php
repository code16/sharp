<?php

namespace App\Sharp\Commands;

use App\Travel;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class TravelSendEmail extends InstanceCommand
{
    public function label(): string
    {
        return 'Send email';
    }

    public function buildCommandConfig(): void
    {
        $this->configureFormModalTitle('Send email')
            ->configureDescription('Will pretend to send an email to all the passengers of this flight.');
    }

    public function execute($instanceId, array $data = []): array
    {
        $this->validate($data, [
            'subject' => 'required|array',
            'message' => 'required|array',
        ]);

        return $this->info('Emails have been sent.');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormHtmlField::make('explanation')
                    ->setInlineTemplate('This message will be sent to the passenger preferred language.'),
            )
            ->addField(
                SharpFormTextField::make('subject')
                    ->setLabel('Subject')
                    ->setLocalized(),
            )
            ->addField(
                SharpFormTextareaField::make('message')
                    ->setLabel('Message')
                    ->setLocalized(),
            );
    }

    protected function initialData($instanceId): array
    {
        return $this
            ->setCustomTransformer('subject', function ($value, Travel $instance) {
                return [
                    'fr' => 'écrire un sujet',
                    'en' => 'write a subject',
                    'it' => 'scrivi un oggetto',
                ];
            })
            ->setCustomTransformer('message', function ($value, Travel $instance) {
                return [
                    'fr' => 'Le vol a été annulé.',
                    'en' => 'The flight has been cancelled.',
                    'it' => 'Il volo è stato cancellato.',
                ];
            })
            ->transform(
                Travel::findOrFail($instanceId),
            );
    }

    public function getDataLocalizations(): array
    {
        return ['en', 'fr', 'it'];
    }
}
