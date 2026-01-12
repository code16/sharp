<?php

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\Testing\SharpAssertions;

pest()->use(SharpAssertions::class);

beforeEach(function () {
    login();
    sharp()->config()->declareEntity(PersonEntity::class);
});

it('gets an entity list', function () {
    fakeListFor(PersonEntity::class, new class() extends PersonList
    {
        public function getListData(): array
        {
            return [
                ['id' => 1, 'name' => 'Marie Curie'],
            ];
        }
    });

    $this->sharpList(PersonEntity::class)
        ->get()
        ->assertOk()
        ->assertListCount(1)
        ->assertListContains(['name' => 'Marie Curie']);
});

it('call an entity list entity command', function () {
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
                        $formFields->addField(SharpFormTextField::make('action'));
                    }

                    public function execute(array $data = []): array
                    {
                        return match ($data['action']) {
                            'info' => $this->info('ok'),
                            'link' => $this->link('https://example.org'),
                        };
                    }
                },
            ];
        }
    });

    $this->sharpList(PersonEntity::class)
        ->callEntityCommand('cmd', ['action' => 'info'])
        ->assertReturnsInfo('ok');

    $this->sharpList(PersonEntity::class)
        ->callEntityCommand('cmd', ['action' => 'link'])
        ->assertReturnsLink('https://example.org');
});
