<?php

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\Enums\PageAlertLevel;
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Exceptions\Form\SharpFormFieldLayoutException;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\PageAlerts\PageAlert;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    sharp()->config()->declareEntity(PersonEntity::class);
    login();
});

it('allows to call an info entity command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function execute(array $data = []): array
                    {
                        return $this->info('ok');
                    }
                },
            ];
        }
    });

    $this->postJson(route('code16.sharp.api.list.command.entity', [
        'filterKey' => 'root',
        'entityKey' => 'person',
        'commandKey' => 'cmd',
    ]))
        ->assertOk()
        ->assertJson([
            'action' => 'info',
            'message' => 'ok',
            'reload' => false,
        ]);
});

it('allows to call a reload entity command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function execute(array $data = []): array
                    {
                        return $this->reload();
                    }
                },
            ];
        }
    });

    $this->postJson(route('code16.sharp.api.list.command.entity', [
        'filterKey' => 'root',
        'entityKey' => 'person',
        'commandKey' => 'cmd',
    ]))
        ->assertOk()
        ->assertJson([
            'action' => 'reload',
        ]);
});

it('allows to call an info + reload entity command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function execute(array $data = []): array
                    {
                        return $this->info('ok', reload: true);
                    }
                },
            ];
        }
    });

    $this->postJson(route('code16.sharp.api.list.command.entity', [
        'filterKey' => 'root',
        'entityKey' => 'person',
        'commandKey' => 'cmd',
    ]))
        ->assertOk()
        ->assertJson([
            'action' => 'info',
            'message' => 'ok',
            'reload' => true,
        ]);
});

it('allows to call a view entity command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function execute(array $data = []): array
                    {
                        return $this->view('welcome');
                    }
                },
            ];
        }
    });

    $this->postJson(route('code16.sharp.api.list.command.entity', [
        'filterKey' => 'root',
        'entityKey' => 'person',
        'commandKey' => 'cmd',
    ]))
        ->assertOk()
        ->assertJson([
            'action' => 'view',
        ]);
});

it('allows to call a html instance command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'my command';
                    }

                    public function execute(array $data = []): array
                    {
                        return $this->html('Hello world');
                    }
                },
            ];
        }
    });

    $this->postJson(route('code16.sharp.api.list.command.entity', [
        'filterKey' => 'root',
        'entityKey' => 'person',
        'commandKey' => 'cmd',
    ]))
        ->assertOk()
        ->assertJson([
            'action' => 'view',
        ]);
});

it('allows to call a refresh entity command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function execute(array $data = []): array
                    {
                        return $this->refresh([1, 3]);
                    }
                },
            ];
        }

        public function getListData(): array|\Illuminate\Contracts\Support\Arrayable
        {
            return collect([
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 2, 'name' => 'Albert Einstein'],
                ['id' => 3, 'name' => 'Niels Bohr'],
            ])->filter(function ($item) {
                return in_array($item['id'], $this->queryParams->specificIds());
            })->values()->all();
        }
    });

    $this->postJson(route('code16.sharp.api.list.command.entity', [
        'filterKey' => 'root',
        'entityKey' => 'person',
        'commandKey' => 'cmd',
    ]))
        ->assertOk()
        ->assertJson([
            'action' => 'refresh',
            'items' => [
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 3, 'name' => 'Niels Bohr'],
            ],
        ]);
});

it('allows to call an link entity command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function execute(array $data = []): array
                    {
                        return $this->link('https://sharp.code16.fr');
                    }
                },
            ];
        }
    });

    $this->postJson(route('code16.sharp.api.list.command.entity', [
        'filterKey' => 'root',
        'entityKey' => 'person',
        'commandKey' => 'cmd',
    ]))
        ->assertOk()
        ->assertJson([
            'action' => 'link',
            'link' => 'https://sharp.code16.fr',
            'openInNewTab' => false,
        ]);
});

it('allows to call an link + openInNewTab entity command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function execute(array $data = []): array
                    {
                        return $this->link('https://sharp.code16.fr', openInNewTab: true);
                    }
                },
            ];
        }
    });

    $this->postJson(route('code16.sharp.api.list.command.entity', [
        'filterKey' => 'root',
        'entityKey' => 'person',
        'commandKey' => 'cmd',
    ]))
        ->assertOk()
        ->assertJson([
            'action' => 'link',
            'link' => 'https://sharp.code16.fr',
            'openInNewTab' => true,
        ]);
});

it('allows to call a form entity command and it handles 422', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('name'));
                    }

                    public function execute(array $data = []): array
                    {
                        $this->validate($data, ['name' => 'required']);

                        return $this->reload();
                    }
                },
            ];
        }
    });

    $this
        ->postJson(
            route('code16.sharp.api.list.command.entity', [
                'filterKey' => 'root',
                'entityKey' => 'person',
                'commandKey' => 'cmd',
            ]),
            ['data' => ['name' => 'Pierre']]
        )
        ->assertOk()
        ->assertJson([
            'action' => 'reload',
        ]);

    $this
        ->postJson(
            route('code16.sharp.api.list.command.entity', [
                'filterKey' => 'root',
                'entityKey' => 'person',
                'commandKey' => 'cmd',
            ]),
            ['data' => ['name' => '']]
        )
        ->assertJsonValidationErrors(['name']);
});

it('allows to validate posted data with the rules() method', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('name'));
                    }

                    public function rules(): array
                    {
                        return ['name' => 'required'];
                    }

                    public function execute(array $data = []): array
                    {
                        return $this->reload();
                    }
                },
            ];
        }
    });

    $this
        ->postJson(
            route('code16.sharp.api.list.command.entity', [
                'filterKey' => 'root',
                'entityKey' => 'person',
                'commandKey' => 'cmd',
            ]),
            ['data' => ['name' => 'Pierre']]
        )
        ->assertOk()
        ->assertJson([
            'action' => 'reload',
        ]);

    $this
        ->postJson(
            route('code16.sharp.api.list.command.entity', [
                'filterKey' => 'root',
                'entityKey' => 'person',
                'commandKey' => 'cmd',
            ]),
            ['data' => ['name' => '']]
        )
        ->assertJsonValidationErrors(['name']);
});

it('allows to call a download entity command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function execute(array $data = []): array
                    {
                        Storage::fake('files');
                        UploadedFile::fake()
                            ->create('account.pdf', 100, 'application/pdf')
                            ->storeAs('pdf', 'account.pdf', ['disk' => 'files']);

                        return $this->download('pdf/account.pdf', 'account.pdf', 'files');
                    }
                },
            ];
        }
    });

    $response = $this
        ->postJson(route('code16.sharp.api.list.command.entity', [
            'filterKey' => 'root',
            'entityKey' => 'person',
            'commandKey' => 'cmd',
        ]))
        ->assertOk();

    expect($response->headers->get('content-disposition'))
        ->toContain('account.pdf');
});

it('allows to call a streamDownload entity command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function execute(array $data = []): array
                    {
                        return $this->streamDownload('content', 'stream.txt');
                    }
                },
            ];
        }
    });

    $response = $this
        ->postJson(route('code16.sharp.api.list.command.entity', [
            'filterKey' => 'root',
            'entityKey' => 'person',
            'commandKey' => 'cmd',
        ]))
        ->assertOk()
        ->assertHeader('content-type', 'text/html; charset=utf-8');

    expect($response->headers->get('content-disposition'))
        ->toContain('stream.txt');
});

it('returns an applicative exception as a 417 as always', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function execute(array $data = []): array
                    {
                        throw new SharpApplicativeException('error');
                    }
                },
            ];
        }
    });

    $this
        ->postJson(route('code16.sharp.api.list.command.entity', [
            'filterKey' => 'root',
            'entityKey' => 'person',
            'commandKey' => 'cmd',
        ]))
        ->assertStatus(417)
        ->assertJson([
            'message' => 'error',
        ]);
});

it('allows to access to the full query in an entity command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function execute(array $data = []): array
                    {
                        return $this->info($this->queryParams->sortedBy()
                            .$this->queryParams->sortedDir());
                    }
                },
            ];
        }
    });

    $this
        ->postJson(route('code16.sharp.api.list.command.entity', [
            'filterKey' => 'root',
            'entityKey' => 'person',
            'commandKey' => 'cmd',
        ]),
            ['query' => ['sort' => 'name', 'dir' => 'desc']]
        )
        ->assertOk()
        ->assertJson([
            'action' => 'info',
            'message' => 'namedesc',
        ]);
});

it('provides selected ids in a bulk command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function buildCommandConfig(): void
                    {
                        $this->configureInstanceSelectionRequired();
                    }

                    public function execute(array $data = []): array
                    {
                        return $this->info(implode('-', $this->selectedIds()));
                    }
                },
            ];
        }
    });
    $this
        ->postJson(route('code16.sharp.api.list.command.entity', [
            'filterKey' => 'root',
            'entityKey' => 'person',
            'commandKey' => 'cmd',
        ]),
            ['query' => ['ids' => ['1', '2']]]
        )
        ->assertOk()
        ->assertJson([
            'action' => 'info',
            'message' => '1-2',
        ]);
});

it('disallows to call an unauthorized entity command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'entity_unauthorized' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function authorize(): bool
                    {
                        return false;
                    }

                    public function execute(array $data = []): array
                    {
                        return $this->reload();
                    }
                },
            ];
        }
    });

    $this
        ->postJson(route('code16.sharp.api.list.command.entity', ['root', 'person', 'entity_unauthorized']))
        ->assertStatus(403);
});

it('returns the form fields of the entity command and build a basic layout if missing', function () {
    $this->withoutExceptionHandling();
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields
                            ->addField(SharpFormTextField::make('name'))
                            ->addField(
                                SharpFormListField::make('jobs')
                                    ->addItemField(
                                        SharpFormTextField::make('company')
                                    )
                            );
                    }

                    public function execute(array $data = []): array
                    {
                        return $this->reload();
                    }
                },
            ];
        }
    });

    $this
        ->getJson(route('code16.sharp.api.list.command.entity.form', [
            'filterKey' => 'root',
            'entityKey' => 'person',
            'commandKey' => 'cmd',
        ]))
        ->assertOk()
        ->assertJsonFragment([
            'layout' => [
                'tabbed' => false,
                'tabs' => [
                    [
                        'columns' => [
                            [
                                'fields' => [
                                    [
                                        [
                                            'key' => 'name',
                                            'size' => 12,
                                        ],
                                    ],
                                    [
                                        [
                                            'key' => 'jobs',
                                            'size' => 12,
                                            'item' => [
                                                [
                                                    [
                                                        'key' => 'company',
                                                        'size' => 12,
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                                'size' => 12,
                            ],
                        ],
                        'title' => '',
                    ],
                ],
            ],
        ]);
});

it('fails when referencing an undeclared form field in the layout', function () {
    $this->withoutExceptionHandling();
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('name'));
                    }

                    public function buildFormLayout(FormLayoutColumn &$column): void
                    {
                        $column->withField('title');
                    }

                    public function execute(array $data = []): array {}
                },
            ];
        }
    });

    $this->getJson(route('code16.sharp.api.list.command.entity.form', [
        'filterKey' => 'root',
        'entityKey' => 'person',
        'commandKey' => 'cmd',
    ]));
})->throws(SharpFormFieldLayoutException::class, SharpFormFieldLayoutException::undeclaredField('title')->getMessage());

it('allows to configure a page alert on an entity command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function buildPageAlert(PageAlert $pageAlert): void
                    {
                        $pageAlert
                            ->setLevelInfo()
                            ->setMessage('My page alert')
                            ->setButton('My button', 'https://example.com');
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('name'));
                    }

                    public function execute(array $data = []): array
                    {
                        return $this->reload();
                    }
                },
            ];
        }
    });

    $this
        ->withoutExceptionHandling()
        ->getJson(route('code16.sharp.api.list.command.entity.form', [
            'filterKey' => 'root',
            'entityKey' => 'person',
            'commandKey' => 'cmd',
        ]))
        ->assertOk()
        ->assertJsonFragment([
            'pageAlert' => [
                'text' => 'My page alert',
                'level' => PageAlertLevel::Info->value,
                'buttonLabel' => 'My button',
                'buttonUrl' => 'https://example.com',
            ],
        ]);
});

it('handles localized form of the entity command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('name')->setLocalized());
                    }

                    public function execute(array $data = []): array
                    {
                        return $this->reload();
                    }

                    public function getDataLocalizations(): array
                    {
                        return ['fr', 'en', 'it'];
                    }
                },
            ];
        }
    });

    $this
        ->getJson(route('code16.sharp.api.list.command.entity.form', [
            'filterKey' => 'root',
            'entityKey' => 'person',
            'commandKey' => 'cmd',
        ]))
        ->assertOk()
        ->assertJsonFragment([
            'fields' => [
                'name' => [
                    'key' => 'name',
                    'type' => 'text',
                    'inputType' => 'text',
                    'localized' => true,
                ],
            ],
            'locales' => ['fr', 'en', 'it'],
        ]);
});

it('allows to initialize form data in an entity command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function buildCommandConfig(): void
                    {
                        $this->configureFormModalTitle(fn ($data) => "Edit {$data['name']}")
                            ->configureFormModalDescription('Custom description');
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('name'));
                    }

                    public function execute(array $data = []): array
                    {
                        return $this->reload();
                    }

                    public function initialData(): array
                    {
                        return [
                            'name' => 'Marie Curie',
                        ];
                    }
                },
            ];
        }
    });

    $this
        ->getJson(route('code16.sharp.api.list.command.entity.form', [
            'filterKey' => 'root',
            'entityKey' => 'person',
            'commandKey' => 'cmd',
        ]))
        ->assertOk()
        ->assertJsonFragment([
            'data' => [
                'name' => 'Marie Curie',
            ],
        ]);
});
