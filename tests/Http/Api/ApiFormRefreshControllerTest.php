<?php

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonShow;
use Code16\Sharp\Tests\Http\Api\Fixtures\ApiFormRefreshControllerEmbed;
use Code16\Sharp\Tests\Http\Api\Fixtures\RefreshFormFields;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function () {
    sharp()->config()->declareEntity(PersonEntity::class);
    login();
});

it('gets updated HTML fields with live refresh of a form', function () {
    fakeFormFor('person', new class() extends SharpForm
    {
        use RefreshFormFields;

        public function buildFormLayout(FormLayout $formLayout): void
        {
            $formLayout->addColumn(12, function (FormLayoutColumn $column) {
                $column->withField('html_string')
                    ->withField('html_non_refreshable')
                    ->withField('html_view')
                    ->withField('html_closure')
                    ->withListField('list', function (FormLayoutColumn $column) {
                        $column->withField('list_name')
                            ->withField('list_html');
                    });
            });
        }

        public function find($id): array
        {
            return $this->transform([]);
        }

        public function update(mixed $id, array $data) {}
    });

    $this->post(
        route('code16.sharp.api.form.refresh.update', [
            'filterKey' => 'root',
            'entityKey' => 'person',
            'instance_id' => 1,
        ]),
        [
            'name' => 'Albert Einstein',
            'html_non_refreshable' => ['text' => 'example'],
            'html_string' => ['text' => 'example'],
            'html_view' => ['text' => 'example'],
            'html_closure' => ['text' => 'example'],
            'list' => [
                ['id' => 1, 'list_name' => 'Marie Curie', 'list_html' => ['text' => 'example']],
            ],
        ]
    )
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->missing('form.data.html_non_refreshable')
            ->where('form.data.html_string', '<h1>Albert Einstein</h1><p>example</p>')
            ->where('form.data.html_view', "<h1>Albert Einstein</h1><p>example</p>\n")
            ->where('form.data.html_closure', '<h1>Albert Einstein</h1><p>example</p>')
            ->where('form.data.list.0.list_html', '<h1>Marie Curie</h1><p>example</p>')
        );
});

it('gets updated HTML fields with live refresh of an entity list entity command form', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    use RefreshFormFields;

                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function execute(array $data = []): array
                    {
                        return [];
                    }
                },
            ];
        }
    });

    $this->post(
        route('code16.sharp.api.form.refresh.update', [
            'filterKey' => 'root',
            'entityKey' => 'person',
            'entity_list_command_key' => 'cmd',
        ]),
        [
            'name' => 'Albert Einstein',
            'html_non_refreshable' => ['text' => 'example'],
            'html_string' => ['text' => 'example'],
            'html_view' => ['text' => 'example'],
            'html_closure' => ['text' => 'example'],
            'list' => [
                ['id' => 1, 'list_name' => 'Marie Curie', 'list_html' => ['text' => 'example']],
            ],
        ]
    )
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->missing('form.data.html_non_refreshable')
            ->where('form.data.html_string', '<h1>Albert Einstein</h1><p>example</p>')
            ->where('form.data.html_view', "<h1>Albert Einstein</h1><p>example</p>\n")
            ->where('form.data.html_closure', '<h1>Albert Einstein</h1><p>example</p>')
            ->where('form.data.list.0.list_html', '<h1>Marie Curie</h1><p>example</p>')
        );
});

it('gets updated HTML fields with live refresh of an entity list instance command form', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'cmd' => new class() extends InstanceCommand
                {
                    use RefreshFormFields;

                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function execute(mixed $instanceId, array $data = []): array
                    {
                        return [];
                    }
                },
            ];
        }
    });

    $this->post(
        route('code16.sharp.api.form.refresh.update', [
            'filterKey' => 'root',
            'entityKey' => 'person',
            'entity_list_command_key' => 'cmd',
            'instance_id' => 1,
        ]),
        [
            'name' => 'Albert Einstein',
            'html_non_refreshable' => ['text' => 'example'],
            'html_string' => ['text' => 'example'],
            'html_view' => ['text' => 'example'],
            'html_closure' => ['text' => 'example'],
            'list' => [
                ['id' => 1, 'list_name' => 'Marie Curie', 'list_html' => ['text' => 'example']],
            ],
        ]
    )
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->missing('form.data.html_non_refreshable')
            ->where('form.data.html_string', '<h1>Albert Einstein</h1><p>example</p>')
            ->where('form.data.html_view', "<h1>Albert Einstein</h1><p>example</p>\n")
            ->where('form.data.html_closure', '<h1>Albert Einstein</h1><p>example</p>')
            ->where('form.data.list.0.list_html', '<h1>Marie Curie</h1><p>example</p>')
        );
});

it('gets updated HTML fields with live refresh of a show instance command form', function () {
    fakeShowFor('person', new class() extends PersonShow
    {
        public function getInstanceCommands(): ?array
        {
            return [
                'cmd' => new class() extends InstanceCommand
                {
                    use RefreshFormFields;

                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function execute(mixed $instanceId, array $data = []): array
                    {
                        return [];
                    }
                },
            ];
        }
    });

    $this->post(
        route('code16.sharp.api.form.refresh.update', [
            'filterKey' => 'root',
            'entityKey' => 'person',
            'show_command_key' => 'cmd',
            'instance_id' => 1,
        ]),
        [
            'name' => 'Albert Einstein',
            'html_non_refreshable' => ['text' => 'example'],
            'html_string' => ['text' => 'example'],
            'html_view' => ['text' => 'example'],
            'html_closure' => ['text' => 'example'],
            'list' => [
                ['id' => 1, 'list_name' => 'Marie Curie', 'list_html' => ['text' => 'example']],
            ],
        ]
    )
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->missing('form.data.html_non_refreshable')
            ->where('form.data.html_string', '<h1>Albert Einstein</h1><p>example</p>')
            ->where('form.data.html_view', "<h1>Albert Einstein</h1><p>example</p>\n")
            ->where('form.data.html_closure', '<h1>Albert Einstein</h1><p>example</p>')
            ->where('form.data.list.0.list_html', '<h1>Marie Curie</h1><p>example</p>')
        );
});

it('gets updated HTML fields with live refresh of an embed form', function () {
    $this
        ->postJson(
            route('code16.sharp.api.form.refresh.update', [
                'filterKey' => 'root',
                'entityKey' => 'person',
                'embed_key' => (new ApiFormRefreshControllerEmbed())->key(),
            ]),
            [
                'name' => 'Albert Einstein',
                'html_non_refreshable' => ['text' => 'example'],
                'html_string' => ['text' => 'example'],
                'html_view' => ['text' => 'example'],
                'html_closure' => ['text' => 'example'],
                'list' => [
                    ['id' => 1, 'list_name' => 'Marie Curie', 'list_html' => ['text' => 'example']],
                ],
            ]
        )
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->missing('form.data.html_non_refreshable')
            ->where('form.data.html_string', '<h1>Albert Einstein</h1><p>example</p>')
            ->where('form.data.html_view', "<h1>Albert Einstein</h1><p>example</p>\n")
            ->where('form.data.html_closure', '<h1>Albert Einstein</h1><p>example</p>')
            ->where('form.data.list.0.list_html', '<h1>Marie Curie</h1><p>example</p>')
        );
});
