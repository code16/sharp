<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class InviteUserCommand extends EntityCommand
{
    public function label(): string
    {
        return "Invite new user...";
    }
    
    public function formModalTitle(): string
    {
        return "Send an invitation to a new user";
    }
    
    public function execute(array $data = []): array
    {
        $this->validate(
            $data,
            [
                "email" => "required|email"
            ]
        );
        
        return $this->info("Invitation planned!");
    }

    function buildFormFields(FieldsContainer $formFields): void
    {
        $this
            ->setGlobalMessage(
                "The invitation will be automatically sent before {{day}}, 10 AM",
                "globalHelp"
            );
        
        $formFields
            ->addField(
                SharpFormTextField::make("email")
                    ->setLabel("E-mail address")
            );
    }
    
    protected function initialData(): array
    {
        return [
            "globalHelp" => [
                "day" => now()->addDay()->formatLocalized("%A")
            ]
        ];
    }
}