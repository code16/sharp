<?php

use Code16\Sharp\Utils\Testing\SharpAssertions;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

uses(SharpAssertions::class);

it('allows to assert_has_authorization', function () {
    $this->initSharpAssertions();

    $response = TestResponse::fromBaseResponse(
        new JsonResponse([
            'authorizations' => [
                'create' => true,
                'update' => false,
            ],
        ]),
    );

    $response->assertSharpHasAuthorization('create');
    $response->assertSharpHasNotAuthorization('update');
});

it('allows to test getSharpForm', function () {
    $fake = new class('a') extends Orchestra\Testbench\TestCase
    {
        use SharpAssertions;

        public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
        {
            $this->uri = $uri;

            return $this;
        }
    };

    $response = $fake->getSharpForm('leaves', 6);

    $this->assertEquals(
        route('code16.sharp.api.form.edit', ['leaves', 6]),
        $response->uri,
    );
});

it('allows to test_updateSharpForm', function () {
    $fake = new class extends Orchestra\Testbench\TestCase
    {
        use SharpAssertions;

        public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
        {
            $this->uri = $uri;
            $this->postedData = get_object_vars(json_decode($content));

            return $this;
        }
    };

    $response = $fake->updateSharpForm('leaves', 6, ['attr' => 'some_value']);

    $this->assertEquals(
        route('code16.sharp.api.form.update', ['leaves', 6]),
        $response->uri,
    );

    $this->assertEquals(
        ['attr' => 'some_value'],
        $response->postedData,
    );
});

it('allows to test_storeSharpForm', function () {
    $fake = new class extends Orchestra\Testbench\TestCase
    {
        use SharpAssertions;

        public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
        {
            $this->uri = $uri;
            $this->postedData = get_object_vars(json_decode($content));

            return $this;
        }
    };

    $response = $fake->storeSharpForm('leaves', ['attr' => 'some_value']);

    $this->assertEquals(
        route('code16.sharp.api.form.store', ['leaves']),
        $response->uri,
    );

    $this->assertEquals(
        ['attr' => 'some_value'],
        $response->postedData,
    );
});

it('allows to test_deleteSharpEntityList', function () {
    $fake = new class extends Orchestra\Testbench\TestCase
    {
        use SharpAssertions;

        public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
        {
            $this->uri = $uri;

            return $this;
        }
    };

    $response = $fake->deleteSharpEntityList('leaves', 6);

    $this->assertEquals(
        route('code16.sharp.api.list.delete', ['leaves', 6]),
        $response->uri,
    );
});

it('allows to test_deleteSharpShow', function () {
    $fake = new class extends Orchestra\Testbench\TestCase
    {
        use SharpAssertions;

        public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
        {
            $this->uri = $uri;

            return $this;
        }
    };

    $response = $fake->deleteSharpShow('leaves', 6);

    $this->assertEquals(
        route('code16.sharp.api.show.delete', ['leaves', 6]),
        $response->uri,
    );
});

it('allows to test_callSharpInstanceCommandFromList', function () {
    $fake = new class extends Orchestra\Testbench\TestCase
    {
        use SharpAssertions;

        public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
        {
            $this->uri = $uri;
            $this->postedData = json_decode($content);

            return $this;
        }
    };

    $response = $fake->callSharpInstanceCommandFromList('leaves', 6, 'command', ['attr' => 'some_value']);

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

it('allows to test_callSharpInstanceCommandFromShow', function () {
    $fake = new class extends Orchestra\Testbench\TestCase
    {
        use SharpAssertions;

        public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
        {
            $this->uri = $uri;
            $this->postedData = json_decode($content);

            return $this;
        }
    };

    $response = $fake->callSharpInstanceCommandFromShow('leaves', 6, 'command', ['attr' => 'some_value']);

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

it('allows to test_callSharpInstanceCommandFromList_with_a_wizard_step', function () {
    $fake = new class extends Orchestra\Testbench\TestCase
    {
        use SharpAssertions;

        public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
        {
            $this->uri = $uri;
            $this->postedData = json_decode($content);

            return $this;
        }
    };

    $response = $fake->callSharpInstanceCommandFromList('leaves', 6, 'command', ['attr' => 'some_value'], 'my-step:123');

    $this->assertEquals('my-step:123', $response->postedData->command_step);
});

it('allows to define_a_current_breadcrumb', function () {
    $fake = new class extends Orchestra\Testbench\TestCase
    {
        use SharpAssertions;

        public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
        {
            $this->referer = $this->defaultHeaders['referer'];

            return $this;
        }
    };

    $response = $fake
        ->withSharpCurrentBreadcrumb([
            ['list', 'trees'],
            ['show', 'trees', 2],
            ['show', 'leaves', 6],
        ])
        ->getSharpForm('leaves', 6);

    $this->assertEquals(
        'http://localhost/sharp/s-list/trees/s-show/trees/2/s-show/leaves/6',
        $response->referer,
    );
});

it('when_no_current_breadcrumb_is_defined_a_default_one_is_set', function () {
    $fake = new class extends Orchestra\Testbench\TestCase
    {
        use SharpAssertions;

        public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
        {
            return $this;
        }
    };

    $response = $fake->getSharpForm('trees', 6);

    $this->assertEquals(
        'http://localhost/sharp/s-list/trees/s-form/trees/6',
        $response->defaultHeaders['referer'],
    );
});
