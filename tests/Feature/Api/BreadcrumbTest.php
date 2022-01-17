<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Http\Context\CurrentSharpRequest;
use Code16\Sharp\Tests\Fixtures\PersonSharpShow;

class BreadcrumbTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('sharp.display_breadcrumb', true);
        $this->login();
    }

    /** @test */
    public function breadcrumb_is_appended_and_built_depending_on_referer_for_API_calls()
    {
        $this->buildTheWorld();

        $this
            ->withHeaders([
                'referer' => url('/sharp/s-list/person/s-show/person/1/s-form/person/1'),
            ])
            ->getJson(route('code16.sharp.api.form.edit', ['person', '1']))
            ->assertJson([
                'breadcrumb' => [
                    'visible' => config('sharp.display_breadcrumb'),
                    'items'   => [
                        [
                            'type'      => 'entityList',
                            'url'       => url('/sharp/s-list/person'),
                            'name'      => 'List',
                            'entityKey' => 'person',
                        ],
                        [
                            'type'      => 'show',
                            'url'       => url('/sharp/s-list/person/s-show/person/1'),
                            'name'      => 'person',
                            'entityKey' => 'person',
                        ],
                        [
                            'type'      => 'form',
                            'url'       => url('/sharp/s-list/person/s-show/person/1/s-form/person/1'),
                            'name'      => 'Edit',
                            'entityKey' => 'person',
                        ],
                    ],
                ],
            ]);
    }

    /** @test */
    public function breadcrumb_is_built_depending_on_current_url_for_web_calls()
    {
        $this->buildTheWorld();

        $this
            ->get(route('code16.sharp.list.subpage', ['person', 's-show/person/1']))
            ->assertOk();

        /** @var CurrentSharpRequest $request */
        $request = app(CurrentSharpRequest::class);

        $this->assertTrue($request->isShow());
        $this->assertCount(2, $request->breadcrumb());
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
                'label' => 'Colleague',
                'show'  => PersonSharpShow::class,
            ]
        );

        $this
            ->withHeaders([
                'referer' => url('/sharp/s-list/person/s-show/person/2/s-show/friend/1'),
            ])
            ->getJson(route('code16.sharp.api.show.show', ['friend', '1']))
            ->assertOk()
            ->assertJson([
                'breadcrumb' => [
                    'visible' => config('sharp.display_breadcrumb'),
                    'items'   => [
                        [
                            'type'      => 'entityList',
                            'url'       => url('/sharp/s-list/person'),
                            'name'      => 'List',
                            'entityKey' => 'person',
                        ],
                        [
                            'type'      => 'show',
                            'url'       => url('/sharp/s-list/person/s-show/person/2'),
                            'name'      => 'Worker',
                            'entityKey' => 'person',
                        ],
                        [
                            'type'      => 'show',
                            'url'       => url('/sharp/s-list/person/s-show/person/2/s-show/friend/1'),
                            'name'      => 'Colleague',
                            'entityKey' => 'friend',
                        ],
                    ],
                ],
            ]);
    }

    /** @test */
    public function if_a_custom_label_is_defined_it_is_used_on_leaves()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.entities.person.show',
            PersonWithBreadcrumbConfigSharpShow::class
        );

        $this
            ->withHeaders([
                'referer' => url('/sharp/s-list/person/s-show/person/1'),
            ])
            ->getJson(route('code16.sharp.api.show.show', ['person', '1']))
            ->assertOk()
            ->assertJson([
                'breadcrumb' => [
                    'visible' => config('sharp.display_breadcrumb'),
                    'items'   => [
                        [
                            'type'      => 'entityList',
                            'url'       => url('/sharp/s-list/person'),
                            'name'      => 'List',
                            'entityKey' => 'person',
                        ],
                        [
                            'type'      => 'show',
                            'url'       => url('/sharp/s-list/person/s-show/person/1'),
                            'name'      => 'John Wayne',
                            'entityKey' => 'person',
                        ],
                    ],
                ],
            ]);
    }
}

class PersonWithBreadcrumbConfigSharpShow extends PersonSharpShow
{
    public function buildShowConfig(): void
    {
        parent::buildShowConfig();
        $this->configureBreadcrumbCustomLabelAttribute('name');
    }
}
