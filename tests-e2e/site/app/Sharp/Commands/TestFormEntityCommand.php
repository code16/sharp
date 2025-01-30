<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class TestFormEntityCommand extends EntityCommand
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

    public function execute(array $data = []): array
    {
        return $this->executeTestFormCommand(null, $data);
    }

    public function authorize(): bool
    {
        return true;
    }
}
