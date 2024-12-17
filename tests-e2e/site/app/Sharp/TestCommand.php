<?php

namespace App\Sharp;

use Code16\Sharp\EntityList\Commands\Wizards\EntityWizardCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class TestCommand extends EntityWizardCommand
{
    public function label(): string
    {
        return 'My entity command';
    }

    public function buildCommandConfig(): void
    {
        $this
            ->configureDescription('My description');
    }

    //    public function execute(array $data = []): array
    //    {
    //        return $this->reload();
    //    }

    public function authorize(): bool
    {
        return true;
    }

    protected function buildFormFieldsForFirstStep(FieldsContainer $formFields): void
    {
        $formFields->addField(
            SharpFormTextField::make('text')
                ->setLabel('Text')
        );
    }

    protected function executeFirstStep(array $data): array
    {
        return $this->toStep('second_step');
    }

    protected function buildFormFieldsForSecondStep(FieldsContainer $formFields): void
    {
        $formFields->addField(
            SharpFormTextField::make('text')
                ->setLabel('Text')
        );
    }

    protected function executeSecondStep(array $data): array
    {
        return $this->info('Done !');
    }
}
