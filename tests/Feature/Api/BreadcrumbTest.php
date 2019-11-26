<?php

namespace Code16\Sharp\Tests\Feature\Api;

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
                    "url" => url('/sharp/list/person')
                ]
            ],
            session("sharp_breadcrumb")
        );

        $this->get('/sharp/show/person/1');
        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person')
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1')
                ]
            ],
            session("sharp_breadcrumb")
        );

        $this->get('/sharp/form/person/1');
        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person')
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1')
                ], [
                    "type" => "form",
                    "url" => url('/sharp/form/person/1')
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
        $this->get('/sharp/show/person/1');
        $this->get('/sharp/form/person/1');

        $this->get('/sharp/list/person');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person')
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
        $this->get('/sharp/form/person');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person')
                ], [
                "type" => "form",
                "url" => url('/sharp/form/person')
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
        $this->get('/sharp/show/person/1');
        $this->get('/sharp/form/person/1');

        $this->get('/sharp/dashboard/personal_dashboard');

        $this->assertEquals(
            [
                [
                    "type" => "dashboard",
                    "url" => url('/sharp/dashboard/personal_dashboard')
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
        $this->get('/sharp/form/person/1');

        $this->get('/sharp/show/person');

        $this->assertEquals(
            [
                [
                    "type" => "show",
                    "url" => url('/sharp/show/person')
                ]
            ],
            session("sharp_breadcrumb")
        );
    }

    /** @test */
    public function breadcrumb_path_is_appended_to_json_responses()
    {
        $this->buildTheWorld();

        $this->get('/sharp/list/person');

        $this->getJson('/sharp/api/list/person')->assertJson([
            "breadcrumb" => [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person')
                ]
            ]
        ]);

        $this->get('/sharp/show/person/1');

        $this->getJson('/sharp/api/show/person/1')->assertJson([
            "breadcrumb" => [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person')
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1')
                ]
            ]
        ]);

        $this->get('/sharp/form/person/1');

        $this->getJson('/sharp/api/form/person/1')->assertJson([
            "breadcrumb" => [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person')
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1')
                ], [
                    "type" => "form",
                    "url" => url('/sharp/form/person/1')
                ]
            ]
        ]);

        $this->get('/sharp/dashboard/personal_dashboard');

        $this->getJson('/sharp/api/dashboard/personal_dashboard')->assertJson([
            "breadcrumb" => [
                [
                    "type" => "dashboard",
                    "url" => url('/sharp/dashboard/personal_dashboard')
                ]
            ]
        ]);
    }

    /** @test */
    public function breadcrumb_path_is_cropped_in_back_cases()
    {
        $this->buildTheWorld();

        $this->get('/sharp/list/person');
        $this->get('/sharp/show/person/1');
        $this->get('/sharp/form/person/1');

        // Go back and forth between Show and Form
        $this->get('/sharp/show/person/1');
        $this->get('/sharp/form/person/1');
        $this->get('/sharp/show/person/1');
        $this->get('/sharp/form/person/1');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person')
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1')
                ], [
                    "type" => "form",
                    "url" => url('/sharp/form/person/1')
                ]
            ],
            session("sharp_breadcrumb")
        );

        $this->get('/sharp/show/person/1');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person')
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1')
                ]
            ],
            session("sharp_breadcrumb")
        );

        $this->get('/sharp/show/person/2');
        $this->get('/sharp/form/person/2');
        $this->get('/sharp/show/person/2');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person')
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1')
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/2')
                ]
            ],
            session("sharp_breadcrumb")
        );

        $this->get('/sharp/show/person/2');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person')
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1')
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/2')
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
                    "url" => url('/sharp/list/person')
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1')
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
                    "url" => url('/sharp/list/person')
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1')
                ], [
                    "type" => "form",
                    "url" => url('/sharp/form/person/1')
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
                    "url" => url('/sharp/list/person')
                ], [
                    "type" => "form",
                    "url" => url('/sharp/form/person/1')
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
                    "url" => url('/sharp/show/person')
                ], [
                    "type" => "form",
                    "url" => url('/sharp/form/person/1')
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
            ->get('/sharp/show/person/1');

        $this->assertEquals(
//            [url('/sharp/list/person?filter_type=4&page=2'), url('/sharp/show/person/1')],
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person?filter_type=4&page=2')
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1')
                ]
            ],
            session("sharp_breadcrumb")
        );

        // Test with bad referer host
        $this->get('/sharp/list/person');
        $this
            ->withHeader("Referer", 'http://some-url.com')
            ->get('/sharp/show/person/1');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person')
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1')
                ]
            ],
            session("sharp_breadcrumb")
        );

        // Test with bad referer path
        $this->get('/sharp/list/person');
        $this
            ->withHeader("Referer", url('/sharp/list/spaceship'))
            ->get('/sharp/show/person/1');

        $this->assertEquals(
            [
                [
                    "type" => "entityList",
                    "url" => url('/sharp/list/person')
                ], [
                    "type" => "show",
                    "url" => url('/sharp/show/person/1')
                ]
            ],
            session("sharp_breadcrumb")
        );
    }
}