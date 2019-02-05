<?php

namespace App\Sharp\Commands;

use App\Spaceship;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
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

    public function description(): string
    {
        return "Will pretend to send a message and increment message count.";
    }

    /**
     * @param string $instanceId
     * @param array $data
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     * @throws SharpApplicativeException
     */
    public function execute($instanceId, array $data = []): array
    {
        $this->validate($data, [
            "message" => "required"
        ]);

        if($data["message"] == "error") {
            throw new SharpApplicativeException("Message can't be «error»");
        }

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

    /**
     * @param $instanceId
     * @return array
     */
    protected function initialData($instanceId): array
    {
        return $this
            ->setCustomTransformer("message", function($value, Spaceship $instance) {
                return sprintf("%s, message #%s", $instance->name, $instance->messages_sent_count);
            })
            ->setCustomTransformer("level", function($value, Spaceship $instance) {
                return array_random(["l","m","h",null]);
            })
            ->transform(
                Spaceship::findOrFail($instanceId)
            );
    }

    public function authorizeFor($instanceId): bool
    {
        return $instanceId%2 == 0 && $instanceId > 10;
    }
}