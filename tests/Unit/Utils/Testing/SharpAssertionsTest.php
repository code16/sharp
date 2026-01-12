<?php

use Code16\Sharp\Filters\SelectFilter;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Code16\Sharp\Tests\Unit\Utils\Testing\SharpAssertionsTestCase;
use Code16\Sharp\Utils\Testing\SharpAssertions;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;

uses(SharpAssertions::class);

beforeEach(function () {
    sharp()->config()->declareEntity(PersonEntity::class);

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
});

it('allows to test entity list', function () {
    /** @var \Mockery\MockInterface|SharpAssertionsTestCase $testMock */
    $testMock = Mockery::mock(SharpAssertionsTestCase::class)->makePartial();

    $testMock->shouldReceive('get')
        ->once()
        ->with(route('code16.sharp.list', [
            'entityKey' => 'person',
            'filter_job' => 'physicist',
        ]))
        ->andReturn(new TestResponse(new Response()));

    $testMock->sharpList('person')
        ->withFilter('job', 'physicist')
        ->get()
        ->assertOk();
});

it('allows to test entity list instance command', function () {
    /** @var \Mockery\MockInterface|SharpAssertionsTestCase $testMock */
    $testMock = Mockery::mock(SharpAssertionsTestCase::class)->makePartial();

    $testMock->shouldReceive('postJson')
        ->once()
        ->with(
            route('code16.sharp.api.list.command.instance', [
                'entityKey' => 'person',
                'instanceId' => 1,
                'commandKey' => 'test',
            ]),
            [
                'data' => ['foo' => 'bar'],
                'query' => ['filter_job' => 'physicist'],
                'command_step' => null,
            ]
        )
        ->andReturn(new TestResponse(new Response()));

    $testMock->sharpList('person')
        ->withFilter('job', 'physicist')
        ->callInstanceCommand(1, 'test', ['foo' => 'bar'])
        ->assertOk();
});

it('allows to test entity list entity command', function () {
    /** @var \Mockery\MockInterface|SharpAssertionsTestCase $testMock */
    $testMock = Mockery::mock(SharpAssertionsTestCase::class)->makePartial();

    $testMock->shouldReceive('postJson')
        ->once()
        ->with(
            route('code16.sharp.api.list.command.entity', [
                'entityKey' => 'person',
                'commandKey' => 'test',
            ]),
            [
                'data' => ['foo' => 'bar'],
                'query' => ['filter_job' => 'physicist'],
                'command_step' => null,
            ]
        )
        ->andReturn(new TestResponse(new Response()));

    $testMock->sharpList('person')
        ->withFilter('job', 'physicist')
        ->callEntityCommand('test', ['foo' => 'bar'])
        ->assertOk();
});
