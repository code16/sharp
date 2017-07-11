<?php

namespace Code16\Sharp\Tests\Unit\EntityList;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\EntityList\Utils\SharpEntityDefaultTestList;

class SharpEntityListCommandTest extends SharpTestCase
{

    /** @test */
    function we_can_get_list_commands_config_with_an_instance()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this->addEntityCommand("entityCommand", new class extends EntityCommand {
                    public function label(): string {
                        return "My Entity Command";
                    }
                    public function execute() {}
                });
                $this->addEntityCommand("instanceCommand", new class extends InstanceCommand {
                    public function label(): string {
                        return "My Instance Command";
                    }
                    public function execute($instanceId) {}
                });
            }
        };

        $this->assertArraySubset([
            "commands" => [
                [
                    "key" => "entityCommand",
                    "label" => "My Entity Command",
                    "type" => "entity"
                ], [
                    "key" => "instanceCommand",
                    "label" => "My Instance Command",
                    "type" => "instance"
                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function we_can_get_list_entity_command_config_with_a_class()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this->addEntityCommand("entityCommand", SharpEntityListCommandTestCommand::class);
            }
        };

        $this->assertArraySubset([
            "commands" => [
                [
                    "key" => "entityCommand",
                    "label" => "My Entity Command",
                    "type" => "entity"
                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function we_can_ask_for_a_confirmation_on_a_command()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this->addEntityCommand("entityCommand", new class extends EntityCommand {
                    public function label(): string {
                        return "My Entity Command";
                    }
                    public function confirmationText() {
                        return "Sure?";
                    }
                    public function execute() {}

                });
            }
        };

        $this->assertArraySubset([
            "commands" => [
                [
                    "key" => "entityCommand",
                    "label" => "My Entity Command",
                    "type" => "entity",
                    "confirmation" => "Sure?"
                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function we_can_define_a_form_on_a_command()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this->addEntityCommand("entityCommand", new class extends EntityCommand {
                    public function label(): string {
                        return "My Entity Command";
                    }
                    public function buildForm() {
                        $this->addField(SharpFormTextField::make("message"));
                    }
                    public function execute() {}
                });
            }
        };

        $this->assertArraySubset([
            "commands" => [
                [
                    "key" => "entityCommand",
                    "label" => "My Entity Command",
                    "type" => "entity",
                    "form" => [
                        "message" => [
                            "key" => "message",
                            "type" => "text",
                            "inputType" => "text"
                        ]
                    ]
                ]
            ]
        ], $list->listConfig());
    }
}

class SharpEntityListCommandTestCommand extends EntityCommand
{

    public function label(): string
    {
        return "My Entity Command";
    }

    public function execute() {}
}