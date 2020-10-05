<?php

namespace App\Sharp\Commands;

use App\User;
use Code16\Sharp\EntityList\Commands\SingleInstanceCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;

class AccountUpdateName extends SingleInstanceCommand
{

    /**
     * @return string
     */
    public function label(): string
    {
        return "Update your name";
    }

    /**
     * @param array $data
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
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

    function buildFormFields()
    {
        $this->addField(
            SharpFormTextField::make("name")
                ->setLabel("Name")
        );
    }

    /**
     * @return array
     */
    protected function initialSingleData(): array
    {
        return $this->transform(User::findOrFail(auth()->id()));
    }

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }
}