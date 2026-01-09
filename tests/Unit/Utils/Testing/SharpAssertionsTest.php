<?php

use Code16\Sharp\Filters\SelectFilter;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Code16\Sharp\Utils\Testing\SharpAssertions;
use Illuminate\Support\Traits\Tappable;

uses(SharpAssertions::class);

beforeEach(function () {
    sharp()->config()->declareEntity(PersonEntity::class);
});

it('allows to test entity list', function () {
    fakeListFor(PersonEntity::class, new class() extends PersonList
    {
        public function getFilters(): ?array
        {
            return [
                new class() extends SelectFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureKey('job');
                    }

                    public function values(): array
                    {
                        return [
                            'physicist' => 'Physicist',
                            'physician' => 'Physician',
                        ];
                    }
                },
            ];
        }
    });

    $response = fakeResponse();
    $response->sharpList('person')
        ->withFilter('job', 'physicist')
        ->get();

    expect($response->uri)->toEqual(
        route('code16.sharp.list', [
            'entityKey' => 'person',
            'filter_job' => 'physicist',
        ])
    );
});

it('allows to test getSharpShow', function () {
    $response = fakeResponse()->getSharpShow('leaves', 6);

    $this->assertEquals(
        route('code16.sharp.show.show', ['s-list/leaves', 'leaves', 6]),
        $response->uri,
    );
});

it('allows to test getSharpForm for edit', function () {
    $response = fakeResponse()->getSharpForm('leaves', 6);

    $this->assertEquals(
        route('code16.sharp.form.edit', ['s-list/leaves', 'leaves', 6]),
        $response->uri,
    );
});

it('allows to test getSharpForm for edit with a custom breadcrumb', function () {
    $response = fakeResponse()
        ->withSharpBreadcrumb(
            fn ($builder) => $builder
                ->appendEntityList('leaves')
                ->appendShowPage('leaves', 6),
        )
        ->getSharpForm('leaves', 6);

    $this->assertEquals(
        route('code16.sharp.form.edit', ['s-list/leaves/s-show/leaves/6', 'leaves', 6]),
        $response->uri,
    );
});

it('allows to test getSharpForm for single edit', function () {
    $response = fakeResponse()->getSharpSingleForm('leaves');

    $this->assertEquals(
        route('code16.sharp.form.edit', ['s-list/leaves', 'leaves']),
        $response->uri,
    );
});

it('allows to test getSharpForm for create', function () {
    $response = fakeResponse()->getSharpForm('leaves');

    $this->assertEquals(
        route('code16.sharp.form.create', ['s-list/leaves', 'leaves']),
        $response->uri,
    );
});

it('allows to test updateSharpForm for update', function () {
    $response = fakeResponse()->updateSharpForm('leaves', 6, ['attr' => 'some_value']);

    $this->assertEquals(
        route('code16.sharp.form.update', ['s-list/leaves', 'leaves', 6]),
        $response->uri,
    );

    $this->assertEquals(
        ['attr' => 'some_value'],
        $response->postedData,
    );
});

it('allows to test updateSharpForm for single update', function () {
    $response = fakeResponse()
        ->updateSharpSingleForm('leaves', ['attr' => 'some_value']);

    $this->assertEquals(
        route('code16.sharp.form.update', ['s-list/leaves', 'leaves']),
        $response->uri,
    );

    $this->assertEquals(
        ['attr' => 'some_value'],
        $response->postedData,
    );
});

it('allows to test updateSharpForm for store', function () {
    $response = fakeResponse()->storeSharpForm('leaves', ['attr' => 'some_value']);

    $this->assertEquals(
        route('code16.sharp.form.store', ['s-list/leaves', 'leaves']),
        $response->uri,
    );

    $this->assertEquals(
        ['attr' => 'some_value'],
        $response->postedData,
    );
});

it('allows to test deleteFromSharpList', function () {
    $response = fakeResponse()->deleteFromSharpList('leaves', 6);

    $this->assertEquals(
        route('code16.sharp.api.list.delete', ['leaves', 6]),
        $response->uri,
    );
});

it('allows to test deleteSharpShow', function () {
    $response = fakeResponse()->deleteFromSharpShow('leaves', 6);

    $this->assertEquals(
        route('code16.sharp.show.delete', ['s-list/leaves', 'leaves', 6]),
        $response->uri,
    );
});

it('allows to test callSharpInstanceCommandFromList', function () {
    $response = fakeResponse()
        ->callSharpInstanceCommandFromList('leaves', 6, 'command', ['attr' => 'some_value']);

    $this->assertEquals(
        route('code16.sharp.api.list.command.instance', [
            'entityKey' => 'leaves',
            'instanceId' => 6,
            'commandKey' => 'command',
        ]),
        $response->uri,
    );

    $this->assertEquals('some_value', $response->postedData->data->attr);
});

it('allows to test callSharpInstanceCommandFromShow', function () {
    $response = fakeResponse()
        ->callSharpInstanceCommandFromShow('leaves', 6, 'command', ['attr' => 'some_value']);

    $this->assertEquals(
        route('code16.sharp.api.show.command.instance', [
            'entityKey' => 'leaves',
            'instanceId' => 6,
            'commandKey' => 'command',
        ]),
        $response->uri,
    );

    $this->assertEquals('some_value', $response->postedData->data->attr);
});

it('allows to test callSharpInstanceCommandFromList with a wizard step', function () {
    $response = fakeResponse()
        ->callSharpInstanceCommandFromList('leaves', 6, 'command', ['attr' => 'some_value'], 'my-step:123');

    $this->assertEquals('my-step:123', $response->postedData->command_step);
});

it('allows to define a current breadcrumb', function () {
    $response = fakeResponse()
        ->withSharpBreadcrumb(
            fn ($builder) => $builder
                ->appendEntityList('trees')
                ->appendShowPage('trees', 2)
                ->appendShowPage('leaves', 6),
        )
        ->getSharpForm('leaves', 6);

    $this->assertEquals(
        'http://localhost/sharp/root/s-list/trees/s-show/trees/2/s-show/leaves/6/s-form/leaves/6',
        $response->uri,
    );
});

it('allows to test getSharpForm for edit with a custom breadcrumb with legacy API', function () {
    $response = fakeResponse()
        ->withSharpCurrentBreadcrumb(
            ['list', 'leaves'],
            ['show', 'leaves', 6],
        )
        ->getSharpForm('leaves', 6);

    $this->assertEquals(
        route('code16.sharp.form.edit', ['s-list/leaves/s-show/leaves/6', 'leaves', 6]),
        $response->uri,
    );
});

it('allows to define a current breadcrumb with legacy API', function () {
    $response = fakeResponse()
        ->withSharpCurrentBreadcrumb(
            ['list', 'trees'],
            ['show', 'trees', 2],
            ['show', 'leaves', 6],
        )
        ->getSharpForm('leaves', 6);

    $this->assertEquals(
        'http://localhost/sharp/root/s-list/trees/s-show/trees/2/s-show/leaves/6/s-form/leaves/6',
        $response->uri,
    );
});

it('allows to test getSharpForm for edit with global filter keys', function () {
    fakeGlobalFilter('test-1');

    $this->assertEquals(
        route('code16.sharp.form.edit', [
            'globalFilter' => 'root',
            'parentUri' => 's-list/leaves',
            'entityKey' => 'leaves',
            'instanceId' => 6,
        ]),
        fakeResponse()
            ->getSharpForm('leaves', 6)
            ->uri,
    );

    $this->assertEquals(
        route('code16.sharp.form.edit', [
            'globalFilter' => 'one',
            'parentUri' => 's-list/leaves',
            'entityKey' => 'leaves',
            'instanceId' => 6,
        ]),
        fakeResponse()
            ->withSharpGlobalFilterValues('one')
            ->getSharpForm('leaves', 6)
            ->uri,
    );

    fakeGlobalFilter('test-2');

    $this->assertEquals(
        route('code16.sharp.form.edit', [
            'globalFilter' => 'one~two',
            'parentUri' => 's-list/leaves',
            'entityKey' => 'leaves',
            'instanceId' => 6,
        ]),
        fakeResponse()
            ->withSharpGlobalFilterValues(['one', 'two'])
            ->getSharpForm('leaves', 6)
            ->uri,
    );
});

function fakeResponse()
{
    return new class('fake') extends Orchestra\Testbench\TestCase
    {
        use SharpAssertions;
        use Tappable;

        public string $uri;
        public mixed $postedData;

        public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
        {
            $this->uri = $uri;

            if ($parameters) {
                $this->postedData = $parameters;
            } elseif ($content) {
                $this->postedData = json_decode($content);
            } else {
                $this->postedData = null;
            }

            return new class($this->uri, $this->postedData) extends \Illuminate\Testing\TestResponse
            {
                public function __construct(public $uri, public $postedData)
                {
                    parent::__construct(new \Illuminate\Http\Response());
                }
            };
        }
    };
}
