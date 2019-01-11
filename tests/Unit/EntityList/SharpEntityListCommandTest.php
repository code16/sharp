<?php

namespace Code16\Sharp\Tests\Unit\EntityList;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\EntityList\EntityListQueryParams;
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
                    public function execute(EntityListQueryParams $params, array $data = []): array {}
                });
                $this->addEntityCommand("instanceCommand", new class extends InstanceCommand {
                    public function label(): string {
                        return "My Instance Command";
                    }
                    public function execute($instanceId, array $data = []): array {}
                });
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            "commands" => [
                'entity' => [
                    [
                        [
                            "key" => "entityCommand",
                            "label" => "My Entity Command",
                            "type" => "entity",
                            "authorization" => true
                        ]
                    ]
                ], 'instance' => [
                    [
                        [
                            "key" => "instanceCommand",
                            "label" => "My Instance Command",
                            "type" => "instance",
                            "authorization" => []
                        ]
                    ]
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

        $list->buildListConfig();

        $this->assertArraySubset([
            "commands" => [
                "entity" => [
                    [
                        [
                            "key" => "entityCommand",
                            "label" => "My Entity Command",
                            "type" => "entity"
                        ]
                    ]
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
                    public function execute(EntityListQueryParams $params, array $data = []): array {}

                });
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            "commands" => [
                "entity" => [
                    [
                        [
                            "key" => "entityCommand",
                            "label" => "My Entity Command",
                            "type" => "entity",
                            "confirmation" => "Sure?"
                        ]
                    ]
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
                    public function buildFormFields() {
                        $this->addField(SharpFormTextField::make("message"));
                    }
                    public function buildFormLayout(FormLayoutColumn &$column) {
                        $column->withSingleField("message");
                    }
                    public function execute(EntityListQueryParams $params, array $data = []): array {}
                });
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            "commands" => [
                "entity" => [
                    [
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
                    public function buildFormFields() {
                        $this->addField(SharpFormTextField::make("message"));
                        $this->addField(SharpFormTextField::make("message2"));
                    }
                    public function execute(EntityListQueryParams $params, array $data = []): array {}
                });
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            "commands" => [
                "entity" => [
                    [
                        [
                            "form" => [
                                "layout" => [
                                    [["key" => "message", "size" => 12, "sizeXS" => 12]],
                                    [["key" => "message2", "size" => 12, "sizeXS" => 12]],
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function we_can_handle_authorization_in_an_entity_command()
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
                    public function execute(EntityListQueryParams $params, array $data = []): array {}
                });
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            "commands" => [
                "entity" => [
                    [
                        [
                            "key" => "entityCommand",
                            "label" => "My Entity Command",
                            "type" => "entity",
                            "authorization" => false,
                        ]
                    ]
                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function we_can_handle_authorization_in_an_instance_command()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this->addInstanceCommand("command", new class extends InstanceCommand {
                    public function label(): string {
                        return "My Instance Command";
                    }
                    public function authorizeFor($instanceId): bool {
                        return $instanceId < 3;
                    }
                    public function execute($instanceId, array $params = []): array {}
                });
            }
        };

        $list->buildListConfig();
        $list->data([
            ["id" => 1], ["id" => 2], ["id" => 3],
            ["id" => 4], ["id" => 5], ["id" => 6],
        ]);

        $this->assertArraySubset([
            "commands" => [
                "instance" => [
                    [
                        [
                            "key" => "command",
                            "label" => "My Instance Command",
                            "type" => "instance",
                            "authorization" => [1,2],
                        ]
                    ]
                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function we_can_define_a_description_on_a_command()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this->addEntityCommand("entityCommand", new class extends EntityCommand {
                    public function label(): string {
                        return "My Entity Command";
                    }
                    public function description(): string {
                        return "My Entity Command description";
                    }
                    public function execute(EntityListQueryParams $params, array $data = []): array {}
                });
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            "commands" => [
                "entity" => [
                    [
                        [
                            "key" => "entityCommand",
                            "label" => "My Entity Command",
                            "description" => "My Entity Command description",
                            "type" => "entity",
                        ]
                    ]
                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function we_can_define_separators_in_instance_commands()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this
                    ->addInstanceCommand("command-1", new class extends InstanceCommand {
                        public function label(): string { return ""; }
                        public function execute($instanceId, array $data = []): array {}
                    })
                    ->addInstanceCommand("command-2", new class extends InstanceCommand {
                        public function label(): string { return ""; }
                        public function execute($instanceId, array $data = []): array {}
                    })
                    ->addInstanceCommandSeparator()
                    ->addInstanceCommand("command-3", new class extends InstanceCommand {
                        public function label(): string { return ""; }
                        public function execute($instanceId, array $data = []): array {}
                    });
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            "commands" => [
                "instance" => [
                    [
                        [
                            "key" => "command-1",
                        ], [
                            "key" => "command-2",
                        ]
                    ], [
                        [
                            "key" => "command-3",
                        ]
                    ]

                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function we_can_define_separators_in_entity_commands()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this
                    ->addEntityCommand("command-1", new class extends EntityCommand {
                        public function label(): string { return ""; }
                        public function execute(EntityListQueryParams $params, array $data = []): array {}
                    })
                    ->addEntityCommandSeparator()
                    ->addEntityCommand("command-2", new class extends EntityCommand {
                        public function label(): string { return ""; }
                        public function execute(EntityListQueryParams $params, array $data = []): array {}
                    })
                    ->addEntityCommand("command-3", new class extends EntityCommand {
                        public function label(): string { return ""; }
                        public function execute(EntityListQueryParams $params, array $data = []): array {}
                    });
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            "commands" => [
                "entity" => [
                    [
                        [
                            "key" => "command-1",
                        ]
                    ], [
                        [
                            "key" => "command-2",
                        ], [
                            "key" => "command-3",
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

    public function execute(EntityListQueryParams $params, array $data = []): array {}
}