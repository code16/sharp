<?php

namespace Code16\Sharp\Tests\Unit\Utils\Testing;

use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Utils\Testing\SharpAssertions;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class SharpAssertionsTest extends SharpTestCase
{
    use SharpAssertions;
    
    /** @test */
    function we_can_assert_has_authorization()
    {
        $this->initSharpAssertions();
        
        $response = TestResponse::fromBaseResponse(
            new JsonResponse([
                "authorizations" => [
                    "create" => true,
                    "update" => false
                ]
            ])
        );
        
        $response->assertSharpHasAuthorization("create");
        $response->assertSharpHasNotAuthorization("update");
    }

    /** @test */
    function we_can_test_getSharpForm()
    {
        $fake = new class extends SharpTestCase {
            use SharpAssertions;
            public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
            {
                $this->uri = $uri;
                return $this;
            }
        };

        $response = $fake->getSharpForm("leaves", 6);

        $this->assertEquals(
            route("code16.sharp.api.form.edit", ["leaves", 6]),
            $response->uri
        );
    }

    /** @test */
    function we_can_test_updateSharpForm()
    {
        $fake = new class extends SharpTestCase {
            use SharpAssertions;
            public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
            {
                $this->uri = $uri;
                $this->postedData = get_object_vars(json_decode($content));
                return $this;
            }
        };

        $response = $fake->updateSharpForm("leaves", 6, ["attr" => "some_value"]);

        $this->assertEquals(
            route("code16.sharp.api.form.update", ["leaves", 6]),
            $response->uri
        );

        $this->assertEquals(
            ["attr" => "some_value"],
            $response->postedData
        );
    }

    /** @test */
    function we_can_test_storeSharpForm()
    {
        $fake = new class extends SharpTestCase {
            use SharpAssertions;
            public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
            {
                $this->uri = $uri;
                $this->postedData = get_object_vars(json_decode($content));
                return $this;
            }
        };

        $response = $fake->storeSharpForm("leaves", ["attr" => "some_value"]);

        $this->assertEquals(
            route("code16.sharp.api.form.store", ["leaves"]),
            $response->uri
        );

        $this->assertEquals(
            ["attr" => "some_value"],
            $response->postedData
        );
    }

    /** @test */
    function we_can_test_deleteSharpForm()
    {
        $fake = new class extends SharpTestCase {
            use SharpAssertions;
            public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
            {
                $this->uri = $uri;
                return $this;
            }
        };

        $response = $fake->deleteSharpForm("leaves", 6);

        $this->assertEquals(
            route("code16.sharp.api.form.delete", ["leaves", 6]),
            $response->uri
        );
    }

    /** @test */
    function we_can_test_callSharpInstanceCommandFromList()
    {
        $fake = new class extends SharpTestCase {
            use SharpAssertions;
            public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
            {
                $this->uri = $uri;
                $this->postedData = collect(get_object_vars(json_decode($content)))
                    ->map(function($item) {
                        return get_object_vars($item);
                    })
                    ->toArray();
                return $this;
            }
        };

        $response = $fake->callSharpInstanceCommandFromList("leaves", 6, "command", ["attr" => "some_value"]);

        $this->assertEquals(
            route("code16.sharp.api.list.command.instance", [
                'entityKey' => "leaves",
                'instanceId' => 6,
                'commandKey' => "command"
            ]),
            $response->uri
        );

        $this->assertEquals(
            ["data" => ["attr" => "some_value"]],
            $response->postedData
        );
    }

    /** @test */
    function we_can_test_callSharpInstanceCommandFromShow()
    {
        $fake = new class extends SharpTestCase {
            use SharpAssertions;
            public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
            {
                $this->uri = $uri;
                $this->postedData = collect(get_object_vars(json_decode($content)))
                    ->map(function($item) {
                        return get_object_vars($item);
                    })
                    ->toArray();
                return $this;
            }
        };

        $response = $fake->callSharpInstanceCommandFromShow("leaves", 6, "command", ["attr" => "some_value"]);

        $this->assertEquals(
            route("code16.sharp.api.show.command.instance", [
                'entityKey' => "leaves",
                'instanceId' => 6,
                'commandKey' => "command"
            ]),
            $response->uri
        );

        $this->assertEquals(
            ["data" => ["attr" => "some_value"]],
            $response->postedData
        );
    }

    /** @test */
    function we_can_define_a_current_breadcrumb()
    {
        $fake = new class extends SharpTestCase {
            use SharpAssertions;
            public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
            {
                $this->referer = $this->defaultHeaders["referer"];
                return $this;
            }
        };
        
        $response = $fake
            ->withSharpCurrentBreadcrumb([
                ["list", "trees"],
                ["show", "trees", 2],
                ["show", "leaves", 6],
            ])
            ->getSharpForm("leaves", 6);
        
        $this->assertEquals(
            "http://localhost/sharp/s-list/trees/s-show/trees/2/s-show/leaves/6",
            $response->referer
        );
    }

    /** @test */
    function when_no_current_breadcrumb_is_defined_a_default_one_is_set()
    {
        $fake = new class extends SharpTestCase {
            use SharpAssertions;
            public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
            {
                return $this;
            }
        };

        $response = $fake->getSharpForm("trees", 6);

        $this->assertEquals(
            "http://localhost/sharp/s-list/trees/s-form/trees/6",
            $response->defaultHeaders["referer"]
        );
    }
}