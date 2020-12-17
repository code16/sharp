<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Http\SharpContext;
use Code16\Sharp\Tests\Fixtures\PersonSharpEntityList;
use Code16\Sharp\Tests\Fixtures\PersonSharpForm;
use Code16\Sharp\Tests\Fixtures\PersonSharpShow;
use Code16\Sharp\Tests\Fixtures\PersonSharpSingleShow;

class BreadcrumbTest extends BaseApiTest
{

    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function breadcrumb_is_built_depending_on_referer_for_API_calls()
    {
        
    }

    /** @test */
    public function breadcrumb_is_built_depending_on_current_url_for_web_calls()
    {

    }

    /** @test */
    public function if_labels_are_defined_for_entities_in_the_config_they_are_used()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.entities.person.label',
            'Worker'
        );

        $this->app['config']->set(
            'sharp.entities.friend',
            [
                "label" => "Colleague",
                "show" => PersonSharpShow::class
            ]
        );

        $this
            ->withHeaders([
                "referer" => url('/sharp/s-list/person/s-show/person/2/s-show/friend/1')
            ])
            ->getJson(route('code16.sharp.api.show.show', ['friend', '1']))
            ->assertOk()
            ->assertJson([
                "breadcrumb" => [
                    "visible" => config("sharp.display_breadcrumb"),
                    "items" => [
                        [
                            "type" => "entityList",
                            "url" => url('/sharp/s-list/person'),
                            "name" => "List",
                            "entityKey" => "person"
                        ],
                        [
                            "type" => "show",
                            "url" => url('/sharp/s-list/person/s-show/person/2'),
                            "name" => "Worker",
                            "entityKey" => "person"
                        ],
                        [
                            "type" => "show",
                            "url" => url('/sharp/s-list/person/s-show/person/2/s-show/friend/1'),
                            "name" => "Colleague",
                            "entityKey" => "friend"
                        ]
                    ]
                ]
            ]);
    }
}