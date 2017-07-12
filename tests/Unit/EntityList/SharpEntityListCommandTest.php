<?php

namespace Code16\Sharp\Tests\Unit\EntityList;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
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
                    public function execute(array $params = []) {}
                });
                $this->addEntityCommand("instanceCommand", new class extends InstanceCommand {
                    public function label(): string {
                        return "My Instance Command";
                    }
                    public function execute($instanceId, array $params = []) {}
                });
            }
        };

        $this->assertArraySubset([
            "commands" => [
                [
                    "key" => "entityCommand",
                    "label" => "My Entity Command",
                    "type" => "entity",
                    "authorization" => true
                ], [
                    "key" => "instanceCommand",
                    "label" => "My Instance Command",
                    "type" => "instance",
                    "authorization" => true
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
                    public function execute(array $params = []) {}

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
                    public function buildFormLayout(FormLayoutColumn &$column) {
                        $column->withSingleField("message");
                    }
                    public function execute(array $params = []) {}
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
                        "fields" => [
                            "message" => [
                                "key" => "message",
                                "type" => "text",
                                "inputType" => "text"
                            ]
                        ],
                        "layout" => [
                            [["key" => "message", "size" => 12, "sizeXS" => 12]]
                        ]
                    ]
                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function if_no_form_layout_is_configured_a_default_is_provided()
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
                        $this->addField(SharpFormTextField::make("message2"));
                    }
                    public function execute(array $params = []) {}
                });
            }
        };

        $this->assertArraySubset([
            "commands" => [
                [
                    "form" => [
                        "layout" => [
                            [["key" => "message", "size" => 12, "sizeXS" => 12]],
                            [["key" => "message2", "size" => 12, "sizeXS" => 12]],
                        ]
                    ]
                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function we_can_handle_authorization_in_the_command()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this->addEntityCommand("entityCommand", new class extends EntityCommand {
                    public function label(): string {
                        return "My Entity Command";
                    }
                    public function authorize(): bool {
                        return false;
                    }
                    public function execute(array $params = []) {}
                });
            }
        };

        $this->assertArraySubset([
            "commands" => [
                [
                    "key" => "entityCommand",
                    "label" => "My Entity Command",
                    "type" => "entity",
                    "authorization" => false,
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

    public function execute(array $params = []) {}
}