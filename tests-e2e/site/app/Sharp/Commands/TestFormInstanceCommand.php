<?php

namespace App\Sharp\Commands;


use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class TestFormInstanceCommand extends InstanceCommand
{
    use TestFormCommand;

    public function label(): string
    {
        return 'Test form command';
    }

    public function buildCommandConfig(): void
    {
        $this->configureDescription('Test description');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $this->buildTestFormCommandFields($formFields);
    }

    public function execute(mixed $instanceId, array $data = []): array
    {
        return $this->executeTestFormCommand($instanceId, $data);
    }

    public function authorize(): bool
    {
        return true;
    }
}
