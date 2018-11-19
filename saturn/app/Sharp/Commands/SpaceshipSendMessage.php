<?php

namespace App\Sharp\Commands;

use App\Spaceship;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;

class SpaceshipSendMessage extends InstanceCommand
{

    /**
     * @return string
     */
    public function label(): string
    {
        return "Send a text message";
    }

    /**
     * @param string $instanceId
     * @param array $data
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function execute($instanceId, array $data = []): array
    {
        $this->validate($data, [
            "message" => "required"
        ]);

        Spaceship::where("id", $instanceId)
            ->increment('messages_sent_count');

        return $this->refresh($instanceId);
    }

    function buildFormFields()
    {
        $this->addField(
            SharpFormTextareaField::make("message")
                ->setLabel("Message")

        )->addField(
            SharpFormCheckField::make("now", "Send right now?")
                ->setHelpMessage("Otherwise it will be sent next night.")

        )->addField(
            SharpFormAutocompleteField::make("level", "local")
                ->setListItemInlineTemplate('{{label}}')
                ->setResultItemInlineTemplate('{{label}}')
                ->setLocalValues([
                    "l" => "Low",
                    "m" => "Medium",
                    "h" => "High",
                ])
                ->setLabel("Level")
        );
    }

    public function authorizeFor($instanceId): bool
    {
        return $instanceId%2 == 0 && $instanceId > 10;
    }
}