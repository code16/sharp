<?php

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\EntityList\Commands\SingleInstanceCommand;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteRemoteField;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonForm;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonShow;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonSingleShow;
use Code16\Sharp\Tests\Http\Api\Fixtures\ApiFormAutocompleteControllerAutocompleteEmbed;
use Code16\Sharp\Utils\Fields\FieldsContainer;

beforeEach(function () {
    sharp()->config()->addEntity('person', PersonEntity::class);

    login();
});

it('allows to call an functional endpoint for a remote autocomplete field', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormAutocompleteRemoteField::make('autocomplete_field')
                    ->setRemoteMethodPOST()
                    ->setRemoteEndpoint('/my/endpoint')
            );
        }
    });

    Route::post('/my/endpoint', function () {
        expect(request()->get('query'))->toBe('my search');

        return [
            ['id' => 1, 'label' => 'John'],
        ];
    });

    $this
        ->postJson(route('code16.sharp.api.form.autocomplete.index', [
            'entityKey' => 'person',
            'autocompleteFieldKey' => 'autocomplete_field',
        ]), [
            'endpoint' => '/my/endpoint',
            'search' => 'my search',
        ])
        ->assertOk()
        ->assertJson([
            'data' => [
                ['id' => 1, 'label' => 'John'],
            ],
        ]);
});

it('allows to call a closure for a remote autocomplete field', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormAutocompleteRemoteField::make('autocomplete_field')
                    ->setRemoteCallback(function ($search) {
                        expect($search)->toBe('my search');

                        return [
                            ['id' => 1, 'label' => 'John'],
                        ];
                    })
            );
        }
    });

    $this
        ->postJson(route('code16.sharp.api.form.autocomplete.index', [
            'entityKey' => 'person',
            'autocompleteFieldKey' => 'autocomplete_field',
        ]), [
            'search' => 'my search',
        ])
        ->assertOk()
        ->assertJson([
            'data' => [
                ['id' => 1, 'label' => 'John'],
            ],
        ]);
});

it('allows to specify linked field passed to the closure', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormTextField::make('name')
                )
                ->addField(
                    SharpFormAutocompleteRemoteField::make('autocomplete_field')
                        ->setRemoteCallback(function ($search, $linkedFields) {
                            expect($search)->toBe('my search')
                                ->and($linkedFields)->toBe(['name' => 'John']);

                            return [
                                ['id' => 1, 'label' => $linkedFields['name']],
                            ];
                        }, linkedFields: ['name'])
                );
        }
    });

    $this
        ->postJson(route('code16.sharp.api.form.autocomplete.index', [
            'entityKey' => 'person',
            'autocompleteFieldKey' => 'autocomplete_field',
        ]), [
            'search' => 'my search',
            'formData' => [
                'name' => 'John',
            ],
        ])
        ->assertOk()
        ->assertJson([
            'data' => [
                ['id' => 1, 'label' => 'John'],
            ],
        ]);
});

it('formats autocomplete results as an array, and handle data wrapper', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormAutocompleteRemoteField::make('autocomplete_field')
                    ->setDataWrapper('wrapper')
                    ->setRemoteMethodGET()
                    ->setRemoteEndpoint('/my/endpoint')
            );
        }
    });

    Route::get('/my/endpoint', fn () => [
        'wrapper' => [
            (object) ['id' => 1, 'label' => 'John'],
            (object) ['id' => 2, 'label' => 'Jane'],
        ],
    ]);

    $this
        ->postJson(route('code16.sharp.api.form.autocomplete.index', [
            'entityKey' => 'person',
            'autocompleteFieldKey' => 'autocomplete_field',
        ]), [
            'endpoint' => '/my/endpoint',
            'search' => 'my search',
        ])
        ->assertOk()
        ->assertJson([
            'data' => [
                ['id' => 1, 'label' => 'John'],
                ['id' => 2, 'label' => 'Jane'],
            ],
        ]);
});

it('renders autocomplete results with template', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormAutocompleteRemoteField::make('autocomplete_field')
                    ->setRemoteMethodPOST()
                    ->setListItemTemplate('{{ $name }}, {{ $job }}')
                    ->setRemoteEndpoint('/my/endpoint')
            );
        }
    });

    Route::post('/my/endpoint', fn () => [
        ['id' => 1, 'name' => 'John', 'job' => 'actor'],
        ['id' => 2, 'name' => 'Jane', 'job' => 'producer'],
    ]);

    $this
        ->postJson(route('code16.sharp.api.form.autocomplete.index', [
            'entityKey' => 'person',
            'autocompleteFieldKey' => 'autocomplete_field',
        ]), [
            'endpoint' => '/my/endpoint',
            'search' => 'my search',
        ])
        ->assertOk()
        ->assertJson([
            'data' => [
                ['id' => 1, 'name' => 'John', 'job' => 'actor', '_html' => 'John, actor'],
                ['id' => 2, 'name' => 'Jane', 'job' => 'producer', '_html' => 'Jane, producer'],
            ],
        ]);
});

it('fails if field is missing', function () {
    $this->withoutExceptionHandling();

    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormTextField::make('name')
            );
        }
    });

    $this
        ->postJson(route('code16.sharp.api.form.autocomplete.index', [
            'entityKey' => 'person',
            'autocompleteFieldKey' => 'autocomplete_field',
        ]));
})->throws(\Code16\Sharp\Exceptions\SharpInvalidConfigException::class);

it('fails if field is not a remote autocomplete field', function () {
    $this->withoutExceptionHandling();

    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormTextField::make('name')
            );
        }
    });

    $this
        ->postJson(route('code16.sharp.api.form.autocomplete.index', [
            'entityKey' => 'person',
            'autocompleteFieldKey' => 'name',
        ]));
})->throws(\Code16\Sharp\Exceptions\SharpInvalidConfigException::class);

it('validates that the sent remote endpoint is the same that was defined in the autocomplete field', function () {
    $this->withoutExceptionHandling();

    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormAutocompleteRemoteField::make('autocomplete_field')
                    ->setRemoteMethodPOST()
                    ->setRemoteEndpoint('/my/endpoint')
            );
        }
    });

    $this
        ->postJson(route('code16.sharp.api.form.autocomplete.index', [
            'entityKey' => 'person',
            'autocompleteFieldKey' => 'autocomplete_field',
        ]), [
            'endpoint' => '/another/endpoint',
            'search' => 'my search',
        ]);
})->throws(\Code16\Sharp\Exceptions\SharpInvalidConfigException::class);

it('allows full and relative remote endpoint', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormAutocompleteRemoteField::make('autocomplete_field')
                        ->setRemoteMethodPOST()
                        ->setRemoteEndpoint('/my/endpoint')
                )
                ->addField(
                    SharpFormAutocompleteRemoteField::make('autocomplete_full_field')
                        ->setRemoteMethodPOST()
                        ->setRemoteEndpoint(url('/my/endpoint'))
                );
        }
    });

    Route::post('/my/endpoint', fn () => []);

    foreach (['/my/endpoint', url('/my/endpoint')] as $endpoint) {
        $this
            ->postJson(route('code16.sharp.api.form.autocomplete.index', [
                'entityKey' => 'person',
                'autocompleteFieldKey' => 'autocomplete_field',
            ]), [
                'endpoint' => $endpoint,
                'search' => 'my search',
            ])
            ->assertOk();

        $this
            ->postJson(route('code16.sharp.api.form.autocomplete.index', [
                'entityKey' => 'person',
                'autocompleteFieldKey' => 'autocomplete_full_field',
            ]), [
                'endpoint' => $endpoint,
                'search' => 'my search',
            ])
            ->assertOk();
    }
});

it('allows dynamic remote endpoint', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormAutocompleteRemoteField::make('autocomplete_field')
                    ->setRemoteMethodPOST()
                    ->setRemoteEndpoint('/my/{{dynamic}}/endpoint')
            );
        }
    });

    Route::post('/my/test/endpoint', fn () => []);

    $this
        ->postJson(route('code16.sharp.api.form.autocomplete.index', [
            'entityKey' => 'person',
            'autocompleteFieldKey' => 'autocomplete_field',
        ]), [
            'endpoint' => url('/my/test/endpoint'),
            'search' => 'my search',
        ])
        ->assertOk();
});

it('won’t allow external remote endpoint', function () {
    $this->withoutExceptionHandling();

    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormAutocompleteRemoteField::make('autocomplete_field')
                    ->setRemoteMethodPOST()
                    ->setRemoteEndpoint('https://google.fr')
            );
        }
    });

    $this
        ->postJson(route('code16.sharp.api.form.autocomplete.index', [
            'entityKey' => 'person',
            'autocompleteFieldKey' => 'autocomplete_field',
        ]), [
            'endpoint' => 'https://google.fr',
            'search' => 'my search',
        ]);
})->throws(\Code16\Sharp\Exceptions\SharpInvalidConfigException::class);

it('allows to call an functional endpoint for a remote autocomplete field in an embed of an Editor field', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormEditorField::make('editor_field')
                    ->allowEmbeds([
                        ApiFormAutocompleteControllerAutocompleteEmbed::class,
                    ])
            );
        }
    });

    Route::post('/my/endpoint', function () {
        expect(request()->get('query'))->toBe('my search');

        return [
            ['id' => 1, 'label' => 'John'],
        ];
    });

    $this
        ->postJson(route('code16.sharp.api.form.autocomplete.index', [
            'entityKey' => 'person',
            'autocompleteFieldKey' => 'autocomplete_field',
            'embed_key' => (new ApiFormAutocompleteControllerAutocompleteEmbed())->key(),
            'endpoint' => '/my/endpoint',
            'search' => 'my search',
        ]))
        ->assertOk()
        ->assertJson([
            'data' => [
                ['id' => 1, 'label' => 'John'],
            ],
        ]);
});

it('allows to call an functional endpoint for a remote autocomplete field in an InstanceCommand of an EntityList', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'my-command' => new class() extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'test';
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(
                            SharpFormAutocompleteRemoteField::make('autocomplete_field')
                                ->setRemoteMethodPOST()
                                ->setRemoteEndpoint('/my/endpoint')
                        );
                    }

                    public function execute(mixed $instanceId, array $data = []): array {}
                },
            ];
        }
    });

    Route::post('/my/endpoint', function () {
        expect(request()->get('query'))->toBe('my search');

        return [
            ['id' => 1, 'label' => 'John'],
        ];
    });

    $this
        ->postJson(route('code16.sharp.api.form.autocomplete.index', [
            'entityKey' => 'person',
            'autocompleteFieldKey' => 'autocomplete_field',
            'instance_id' => 1,
            'entity_list_command_key' => 'my-command',
            'endpoint' => '/my/endpoint',
            'search' => 'my search',
        ]))
        ->assertOk()
        ->assertJson([
            'data' => [
                ['id' => 1, 'label' => 'John'],
            ],
        ]);
});

it('allows to call an functional endpoint for a remote autocomplete field in an EntityCommand of an EntityList', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'my-command' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'test';
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(
                            SharpFormAutocompleteRemoteField::make('autocomplete_field')
                                ->setRemoteMethodPOST()
                                ->setRemoteEndpoint('/my/endpoint')
                        );
                    }

                    public function execute(array $data = []): array {}
                },
            ];
        }
    });

    Route::post('/my/endpoint', function () {
        expect(request()->get('query'))->toBe('my search');

        return [
            ['id' => 1, 'label' => 'John'],
        ];
    });

    $this
        ->postJson(route('code16.sharp.api.form.autocomplete.index', [
            'entityKey' => 'person',
            'autocompleteFieldKey' => 'autocomplete_field',
            'entity_list_command_key' => 'my-command',
            'endpoint' => '/my/endpoint',
            'search' => 'my search',
        ]))
        ->assertOk()
        ->assertJson([
            'data' => [
                ['id' => 1, 'label' => 'John'],
            ],
        ]);
});

it('allows to call an functional endpoint for a remote autocomplete field in an InstanceCommand of a Show', function () {
    fakeShowFor('person', new class() extends PersonShow
    {
        public function getInstanceCommands(): ?array
        {
            return [
                'my-command' => new class() extends SingleInstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'test';
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(
                            SharpFormAutocompleteRemoteField::make('autocomplete_field')
                                ->setRemoteMethodPOST()
                                ->setRemoteEndpoint('/my/endpoint')
                        );
                    }

                    protected function executeSingle(array $data): array {}
                },
            ];
        }
    });

    Route::post('/my/endpoint', function () {
        expect(request()->get('query'))->toBe('my search');

        return [
            ['id' => 1, 'label' => 'John'],
        ];
    });

    $this
        ->postJson(route('code16.sharp.api.form.autocomplete.index', [
            'entityKey' => 'person',
            'autocompleteFieldKey' => 'autocomplete_field',
            'instance_id' => 1,
            'show_command_key' => 'my-command',
            'endpoint' => '/my/endpoint',
            'search' => 'my search',
        ]))
        ->assertOk()
        ->assertJson([
            'data' => [
                ['id' => 1, 'label' => 'John'],
            ],
        ]);
});

it('allows to call an functional endpoint for a remote autocomplete field in an InstanceCommand of a SingleShow', function () {
    fakeShowFor('person', new class() extends PersonSingleShow
    {
        public function getInstanceCommands(): ?array
        {
            return [
                'my-command' => new class() extends SingleInstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'test';
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(
                            SharpFormAutocompleteRemoteField::make('autocomplete_field')
                                ->setRemoteMethodPOST()
                                ->setRemoteEndpoint('/my/endpoint')
                        );
                    }

                    protected function executeSingle(array $data): array {}
                },
            ];
        }
    });

    Route::post('/my/endpoint', function () {
        expect(request()->get('query'))->toBe('my search');

        return [
            ['id' => 1, 'label' => 'John'],
        ];
    });

    $this
        ->postJson(route('code16.sharp.api.form.autocomplete.index', [
            'entityKey' => 'person',
            'autocompleteFieldKey' => 'autocomplete_field',
            'show_command_key' => 'my-command',
            'endpoint' => '/my/endpoint',
            'search' => 'my search',
        ]))
        ->assertOk()
        ->assertJson([
            'data' => [
                ['id' => 1, 'label' => 'John'],
            ],
        ]);
});

it('won’t allow to call an functional endpoint for a remote autocomplete field in an InstanceCommand when not authorized', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'my-command' => new class() extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'test';
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(
                            SharpFormAutocompleteRemoteField::make('autocomplete_field')
                                ->setRemoteMethodPOST()
                                ->setRemoteEndpoint('/my/endpoint')
                        );
                    }

                    public function authorizeFor(mixed $instanceId): bool
                    {
                        return false;
                    }

                    public function execute(mixed $instanceId, array $data = []): array {}
                },
            ];
        }
    });

    Route::post('/my/endpoint', function () {
        expect(request()->get('query'))->toBe('my search');

        return [
            ['id' => 1, 'label' => 'John'],
        ];
    });

    $this
        ->postJson(route('code16.sharp.api.form.autocomplete.index', [
            'entityKey' => 'person',
            'autocompleteFieldKey' => 'autocomplete_field',
            'instance_id' => 1,
            'entity_list_command_key' => 'my-command',
            'endpoint' => '/my/endpoint',
            'search' => 'my search',
        ]))
        ->assertForbidden();
});
