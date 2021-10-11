<?php

namespace App\Sharp\Commands;

use App\User;
use Code16\Sharp\EntityList\Commands\SingleInstanceCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class AccountUpdateName extends SingleInstanceCommand
{
    public function label(): string
    {
        return "Update your name";
    }

    public function executeSingle(array $data = []): array
    {
        $this->validate($data, [
            "name" => "required"
        ]);

        User::findOrFail(auth()->id())->update([
            "name" => $data["name"]
        ]);

        return $this->reload();
    }

    function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields->addField(
            SharpFormTextField::make("name")
                ->setLabel("Name")
        );
    }

    protected function initialSingleData(): array
    {
        return $this->transform(User::findOrFail(auth()->id()));
    }

    public function authorize(): bool
    {
        return true;
    }
}