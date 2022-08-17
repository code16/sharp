<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Tests\Fixtures\PersonSharpForm;
use Code16\Sharp\Tests\Fixtures\PersonSharpShow;
use Code16\Sharp\Tests\Fixtures\PersonSharpValidator;
use Code16\Sharp\Tests\Unit\Utils\WithCurrentSharpRequestFake;
use Code16\Sharp\Utils\Entities\SharpEntityManager;

class FormControllerTest extends BaseApiTest
{
    use WithCurrentSharpRequestFake;

    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function we_can_get_form_data_for_an_entity()
    {
        $this->buildTheWorld();

        $this->getJson('/sharp/api/form/person/1')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'John Wayne',
                ],
            ]);
    }

    /** @test */
    public function we_can_get_form_initial_data_for_an_entity_in_creation()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this->getJson('/sharp/api/form/person')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'default name',
                ],
            ]);
    }

    /** @test */
    public function we_wont_get_entity_attribute_for_a_non_form_data()
    {
        $this->buildTheWorld();

        $result = $this->getJson('/sharp/api/form/person/1');

        $this->assertArrayHasKey('name', $result->json()['data']);
        $this->assertArrayNotHasKey('job', $result->json()['data']);
    }

    /** @test */
    public function we_can_get_form_fields_for_an_entity()
    {
        $this->buildTheWorld();

        $this->getJson('/sharp/api/form/person/1')
            ->assertStatus(200)
            ->assertJson([
                'fields' => [
                    'name' => [
                        'type' => 'text',
                    ],
                ],
            ]);
    }

    /** @test */
    public function we_can_get_form_layout_for_an_entity()
    {
        $this->buildTheWorld();

        $this->getJson('/sharp/api/form/person/1')
            ->assertStatus(200)
            ->assertJson([
                'layout' => [
                    'tabbed' => true,
                    'tabs' => [[
                        'title' => 'one',
                        'columns' => [
                            [
                                'size' => 6,
                                'fields' => [
                                    [
                                        ['key' => 'name'],
                                    ],
                                ],
                            ],
                        ],
                    ]],
                ],
            ]);
    }

    /** @test */
    public function we_can_update_an_entity()
    {
        $this->buildTheWorld();
        $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person/s-show/person/1/s-form/person/1');

        $this
            ->postJson('/sharp/api/form/person/1', [
                'name' => 'Jane Fonda',
            ])
            ->assertOk()
            ->assertJson([
                'redirectUrl' => url('/sharp/s-list/person/s-show/person/1'),
            ]);
    }

    /** @test */
    public function we_can_delete_an_entity()
    {
        $this->buildTheWorld();

        $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person/s-form/person/1');
        $this->deleteJson('/sharp/api/form/person/1')
            ->assertStatus(200)
            ->assertJson([
                'redirectUrl' => url('/sharp/s-list/person'),
            ]);
    }

    /** @test */
    public function when_deleting_an_entity_with_a_show_we_are_redirected_to_the_entity_list()
    {
        $this->buildTheWorld();

        $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person/s-show/person/1/s-form/person/1');
        $this->deleteJson('/sharp/api/form/person/1')
            ->assertStatus(200)
            ->assertJson([
                'redirectUrl' => url('/sharp/s-list/person'),
            ]);
    }

    /** @test */
    public function when_deleting_an_entity_with_multiple_shows_we_are_redirected_to_the_parent_show()
    {
        $this->withoutExceptionHandling();
        $this->buildTheWorld();

        // Some fake config to avoid 404
        $this->app['config']->set('sharp.entities.car.form', PersonSharpForm::class);
        $this->app['config']->set('sharp.entities.car.show', PersonSharpShow::class);

        $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person/s-show/person/1/s-show/car/2/s-form/car/2');
        $this->deleteJson('/sharp/api/form/car/2')
            ->assertOk()
            ->assertJson([
                'redirectUrl' => url('/sharp/s-list/person/s-show/person/1'),
            ]);
    }

    /** @test */
    public function we_can_validate_an_entity_before_update()
    {
        $this->buildTheWorld();
        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setValidator(PersonSharpValidator::class);

        $this
            ->postJson('/sharp/api/form/person/1', [
                'age' => 22,
            ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name field is required.',
                    ],
                ],
            ]);
    }

    /** @test */
    public function we_can_store_a_new_entity()
    {
        $this->buildTheWorld();
        $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person/s-form/person');

        $this
            ->postJson('/sharp/api/form/person', [
                'name' => 'Jane Fonda',
            ])
            ->assertOk()
            ->assertJson([
                'redirectUrl' => url('/sharp/s-list/person'),
            ]);
    }

    /** @test */
    public function we_can_store_a_new_entity_and_redirect_to_the_show_page()
    {
        $this->buildTheWorld();

        app()->bind(
            PersonSharpForm::class,
            function () {
                return new class extends PersonSharpForm
                {
                    protected bool $displayShowPageAfterCreation = true;
                };
            });

        $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person/s-form/person');

        $this
            ->postJson('/sharp/api/form/person', [
                'name' => 'Jane Fonda',
            ])
            ->assertOk()
            ->assertJson([
                'redirectUrl' => url('/sharp/s-list/person/s-show/person/1'),
            ]);
    }

    /** @test */
    public function invalid_entity_key_is_returned_as_404()
    {
        $this->buildTheWorld();

        $this->getJson('/sharp/api/form/notanvalidentity')
            ->assertStatus(404);
    }

    /** @test */
    public function applicative_exception_is_returned_as_417()
    {
        $this->buildTheWorld();

        $this
            ->postJson('/sharp/api/form/person/notanid', [
                'name' => 'Jane Fonda',
            ])
            ->assertStatus(417)
            ->assertJson([
                'message' => 'notanid is not a valid id',
            ]);
    }

    /** @test */
    public function we_can_get_form_data_for_an_entity_on_a_single_form_case()
    {
        $this->buildTheWorld(true);

        $this->getJson('/sharp/api/form/person')
            ->assertOk()
            ->assertJson(['data' => [
                'name' => 'Single John Wayne',
            ]]);

        $this->getJson('/sharp/api/form/person/1')
            ->assertStatus(404);
    }

    /** @test */
    public function we_can_update_an_entity_on_a_single_form_case()
    {
        $this->buildTheWorld(true);
        $this->fakeCurrentSharpRequestWithUrl('/sharp/s-show/person/s-form/person');

        $this
            ->postJson('/sharp/api/form/person', [
                'name' => 'Jane Fonda',
            ])
            ->assertOk()
            ->assertJson([
                'redirectUrl' => url('/sharp/s-show/person'),
            ]);

        $this
            ->postJson('/sharp/api/form/person/1', [])
            ->assertStatus(404);
    }

    /** @test */
    public function we_cant_delete_an_entity_on_a_single_form_case()
    {
        $this->buildTheWorld(true);

        $this->deleteJson('/sharp/api/form/person')
            ->assertStatus(500);

        $this->deleteJson('/sharp/api/form/person/1')
            ->assertStatus(404);
    }
}
