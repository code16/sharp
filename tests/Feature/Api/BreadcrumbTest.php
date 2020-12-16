<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Http\SharpContext;
use Code16\Sharp\Tests\Fixtures\PersonSharpEntityList;
use Code16\Sharp\Tests\Fixtures\PersonSharpForm;
use Code16\Sharp\Tests\Fixtures\PersonSharpSingleShow;

class BreadcrumbTest extends BaseApiTest
{

    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function breadcrumb_path_is_stored_as_we_navigate()
    {
        $this->buildTheWorld();

        $this->get('/sharp/list/person');
        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person'),
                    "name" => "List",
                ]
            ],
            session("sharp_breadcrumb")
        );

        $this->get('/sharp/show/person/1?x-access-from=ui');
        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person'),
                    "name" => "List",
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1'),
                    "name" => "person",
                ]
            ],
            session("sharp_breadcrumb")
        );

        $this->get('/sharp/form/person/1?x-access-from=ui');
        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person'),
                    "name" => "List",
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1'),
                    "name" => "person",
                ], [
                    "type" => "form",
                    "url" => url('/sharp/form/person/1'),
                    "name" => "Edit",
                ]
            ],
            session("sharp_breadcrumb")
        );
    }

    /** @test */
    public function breadcrumb_path_is_reset_if_we_navigate_to_a_list()
    {
        $this->buildTheWorld();

        $this->get('/sharp/list/person');
        $this->get('/sharp/show/person/1?x-access-from=ui');
        $this->get('/sharp/form/person/1?x-access-from=ui');

        $this->get('/sharp/list/person');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person'),
                    "name" => "List",
                ]
            ],
            session("sharp_breadcrumb")
        );
    }
    
    /** @test */
    public function breadcrumb_path_is_updated_if_we_navigate_to_a_form_in_create_mode()
    {
        $this->buildTheWorld();

        $this->get('/sharp/list/person');
        $this->get('/sharp/form/person?x-access-from=ui');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person'),
                    "name" => "List",
                ], [
                    "type" => "form",
                    "url" => url('/sharp/form/person'),
                    "name" => "New “person”",
                ]
            ],
            session("sharp_breadcrumb")
        );
    }

    /** @test */
    public function breadcrumb_path_is_reset_if_we_navigate_to_a_dashboard()
    {
        $this->buildTheWorld();

        $this->get('/sharp/list/person');
        $this->get('/sharp/show/person/1?x-access-from=ui');
        $this->get('/sharp/form/person/1?x-access-from=ui');

        $this->get('/sharp/dashboard/personal_dashboard');

        $this->assertEquals(
            [
                [
                    "type" => "dashboard",
                    "url" => url('/sharp/dashboard/personal_dashboard'),
                    "name" => "Dashboard",
                ]
            ],
            session("sharp_breadcrumb")
        );
    }

    /** @test */
    public function breadcrumb_path_is_reset_if_we_navigate_to_a_single_show()
    {
        $this->buildTheWorld(true);

        $this->get('/sharp/list/person');
        $this->get('/sharp/form/person/1?x-access-from=ui');

        $this->get('/sharp/show/person?x-access-from=ui');

        $this->assertEquals(
            [
                [
                    "type" => "show",
                    "url" => url('/sharp/show/person'),
                    "name" => "person"
                ]
            ],
            session("sharp_breadcrumb")
        );
    }

    /** @test */
    public function breadcrumb_path_is_appended_to_json_responses()
    {
        $this->buildTheWorld();
        config()->set("sharp.display_breadcrumb", true);

        $this->get('/sharp/list/person');

        $this->getJson('/sharp/api/list/person')->assertJson([
            "breadcrumb" => [
                "items" => [
                    [
                        "type" => "entityList",
                        "url" => url('/sharp/list/person'),
                        "name" => "List"
                    ]
                ],
                "visible" => true
            ]
        ]);

        $this->get('/sharp/show/person/1?x-access-from=ui');

        $this->getJson('/sharp/api/show/person/1')->assertJson([
            "breadcrumb" =>  [
                "items" => [
                    [
                        "type" => "entityList",
                        "url" => url('/sharp/list/person'),
                        "name" => "List"
                    ], [
                        "type" => "show",
                        "url" => url('/sharp/show/person/1'),
                        "name" => "person"
                    ]
                ],
                "visible" => true
            ]
        ]);

        $this->get('/sharp/form/person/1?x-access-from=ui');

        $this->getJson('/sharp/api/form/person/1')->assertJson([
            "breadcrumb" => [
                "items" => [
                    [
                        "type" => "entityList",
                        "url" => url('/sharp/list/person'),
                        "name" => "List"
                    ], [
                        "type" => "show",
                        "url" => url('/sharp/show/person/1'),
                        "name" => "person"
                    ], [
                        "type" => "form",
                        "url" => url('/sharp/form/person/1'),
                        "name" => "Edit"
                    ]
                ],
                "visible" => true
            ]
        ]);

        $this->get('/sharp/dashboard/personal_dashboard');

        $this->getJson('/sharp/api/dashboard/personal_dashboard')->assertJson([
            "breadcrumb" => [
                "items" => [
                    [
                        "type" => "dashboard",
                        "url" => url('/sharp/dashboard/personal_dashboard'),
                        "name" => "Dashboard"
                    ]
                ],
                "visible" => true
            ]
        ]);
    }

    /** @test */
    public function breadcrumb_path_is_cropped_in_back_cases()
    {
        $this->buildTheWorld();

        $this->get('/sharp/list/person');
        $this->get('/sharp/show/person/1?x-access-from=ui');
        $this->get('/sharp/form/person/1?x-access-from=ui');

        // Go back and forth between Show and Form
        $this->get('/sharp/show/person/1?x-access-from=ui');
        $this->get('/sharp/form/person/1?x-access-from=ui');
        $this->get('/sharp/show/person/1?x-access-from=ui');
        $this->get('/sharp/form/person/1?x-access-from=ui');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person'),
                    "name" => "List"
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1'),
                    "name" => "person"
                ], [
                    "type" => "form",
                    "url" => url('/sharp/form/person/1'),
                    "name" => "Edit"
                ]
            ],
            session("sharp_breadcrumb")
        );

        $this->get('/sharp/show/person/1?x-access-from=ui');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person'),
                    "name" => "List"
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1'),
                    "name" => "person"
                ]
            ],
            session("sharp_breadcrumb")
        );

        $this->get('/sharp/show/person/2?x-access-from=ui');
        $this->get('/sharp/form/person/2?x-access-from=ui');
        $this->get('/sharp/show/person/2?x-access-from=ui');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person'),
                    "name" => "List"
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1'),
                    "name" => "person"
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/2'),
                    "name" => "person"
                ]
            ],
            session("sharp_breadcrumb")
        );

        $this->get('/sharp/show/person/2?x-access-from=ui');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person'),
                    "name" => "List"
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1'),
                    "name" => "person"
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/2'),
                    "name" => "person"
                ]
            ],
            session("sharp_breadcrumb")
        );
    }

    /** @test */
    public function direct_access_to_a_show_adds_a_list_url_as_previous_segment()
    {
        $this->buildTheWorld();

        $this->get('/sharp/show/person/1');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person'),
                    "name" => "List"
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1'),
                    "name" => "person"
                ]
            ],
            session("sharp_breadcrumb")
        );
    }

    /** @test */
    public function direct_access_to_a_form_adds_a_list_and_a_show_url_as_previous_segment()
    {
        $this->buildTheWorld();

        $this->get('/sharp/form/person/1');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person'),
                    "name" => "List"
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1'),
                    "name" => "person"
                ], [
                    "type" => "form",
                    "url" => url('/sharp/form/person/1'),
                    "name" => "Edit"
                ]
            ],
            session("sharp_breadcrumb")
        );
    }

    /** @test */
    public function direct_access_to_a_form_adds_a_list_url_as_previous_segment_if_there_is_no_show()
    {
        $this->app['config']->set(
            'sharp.entities.person.list', PersonSharpEntityList::class
        );

        $this->app['config']->set(
            'sharp.entities.person.form', PersonSharpForm::class
        );

        $this->get('/sharp/form/person/1');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person'),
                    "name" => "List"
                ], [
                    "type" => "form",
                    "url" => url('/sharp/form/person/1'),
                    "name" => "Edit"
                ]
            ],
            session("sharp_breadcrumb")
        );
    }

    /** @test */
    public function direct_access_to_a_form_adds_a_show_url_as_previous_segment_in_case_of_single_show()
    {
        $this->app['config']->set(
            'sharp.entities.person.show', PersonSharpSingleShow::class
        );

        $this->app['config']->set(
            'sharp.entities.person.form', PersonSharpForm::class
        );

        $this->get('/sharp/form/person/1');

        $this->assertEquals(
            [
                [
                    "type" => "show",
                    "url" => url('/sharp/show/person'),
                    "name" => "person"
                ], [
                    "type" => "form",
                    "url" => url('/sharp/form/person/1'),
                    "name" => "Edit"
                ]
            ],
            session("sharp_breadcrumb")
        );
    }

    /** @test */
    public function previous_breadcrumb_item_is_updated_regarding_referer_to_manage_updated_querystring()
    {
        $this->buildTheWorld();

        $this->get('/sharp/list/person');
        $this
            ->withHeader("Referer", url('/sharp/list/person?filter_type=4&page=2'))
            ->get('/sharp/show/person/1?x-access-from=ui');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person?filter_type=4&page=2'),
                    "name" => "List"
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1'),
                    "name" => "person"
                ]
            ],
            session("sharp_breadcrumb")
        );

        // Test with bad referer host
        $this->get('/sharp/list/person');
        $this
            ->withHeader("Referer", 'http://some-url.com')
            ->get('/sharp/show/person/1?x-access-from=ui');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person'),
                    "name" => "List"
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1'),
                    "name" => "person"
                ]
            ],
            session("sharp_breadcrumb")
        );

        // Test with bad referer path
        $this->get('/sharp/list/person');
        $this
            ->withHeader("Referer", url('/sharp/list/spaceship'))
            ->get('/sharp/show/person/1?x-access-from=ui');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person'),
                    "name" => "List"
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1'),
                    "name" => "person"
                ]
            ],
            session("sharp_breadcrumb")
        );
    }

    /** @test */
    public function we_can_get_breadcrumb_parts_with_sharp_context()
    {
        $this->buildTheWorld();

        $this->get('/sharp/list/person');

        $this->assertEquals(
            ["list", "person"],
            app(SharpContext::class)->getPreviousPageFromBreadcrumb()->toArray()
        );
        $this->assertEquals(
            ["list", "person"],
            app(SharpContext::class)->getPreviousPageFromBreadcrumb("list")->toArray()
        );
        $this->assertNull(
            app(SharpContext::class)->getPreviousPageFromBreadcrumb("form")
        );

        $this->get('/sharp/show/person/1');

        $this->assertEquals(
            ["show", "person", "1"],
            app(SharpContext::class)->getPreviousPageFromBreadcrumb()->toArray()
        );
        $this->assertEquals(
            ["show", "person", "1"],
            app(SharpContext::class)->getPreviousPageFromBreadcrumb("show")->toArray()
        );
        $this->assertEquals(
            ["list", "person"],
            app(SharpContext::class)->getPreviousPageFromBreadcrumb("list")->toArray()
        );

        $this->get('/sharp/form/person/1');

        $this->assertEquals(
            ["form", "person", "1"],
            app(SharpContext::class)->getPreviousPageFromBreadcrumb()->toArray()
        );
        $this->assertEquals(
            ["form", "person", "1"],
            app(SharpContext::class)->getPreviousPageFromBreadcrumb("form")->toArray()
        );
        $this->assertEquals(
            ["show", "person", "1"],
            app(SharpContext::class)->getPreviousPageFromBreadcrumb("show")->toArray()
        );
        $this->assertEquals(
            ["list", "person"],
            app(SharpContext::class)->getPreviousPageFromBreadcrumb("list")->toArray()
        );
    }

    /** @test */
    public function breadcrumb_path_is_reset_if_we_access_content_through_direct_urls()
    {
        $this->buildTheWorld();

        $this->get('/sharp/list/person');
        $this->get('/sharp/show/person/1?x-access-from=ui');
        $this->get('/sharp/show/person/2');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person'),
                    "name" => "List"
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/2'),
                    "name" => "person"
                ]
            ],
            session("sharp_breadcrumb")
        );

        $this->get('/sharp/list/person');
        $this->get('/sharp/show/person/1?x-access-from=ui');
        $this->get('/sharp/form/person/2');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person'),
                    "name" => "List"
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/2'),
                    "name" => "person"
                ], [
                    "type" => "form",
                    "url" => url('/sharp/form/person/2'),
                    "name" => "Edit"
                ]
            ],
            session("sharp_breadcrumb")
        );
    }
}