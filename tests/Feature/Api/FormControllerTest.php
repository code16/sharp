<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpSingleForm;
use Illuminate\Contracts\Support\Arrayable;

class FormControllerTest extends BaseApiTest
{
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
            ->assertJson(["data" => [
                "name" => "John Wayne"
            ]]);
    }

    /** @test */
    public function we_can_get_form_initial_data_for_an_entity_in_creation()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this->getJson('/sharp/api/form/person')
            ->assertStatus(200)
            ->assertJson(["data" => [
                "name" => "default name"
            ]]);
    }

    /** @test */
    public function we_wont_get_entity_attribute_for_a_non_form_data()
    {
        $this->buildTheWorld();

        $result = $this->getJson('/sharp/api/form/person/1');

        $this->assertArrayHasKey("name", $result->json()["data"]);
        $this->assertArrayNotHasKey("job", $result->json()["data"]);
    }

    /** @test */
    public function we_can_get_form_fields_for_an_entity()
    {
        $this->buildTheWorld();

        $this->getJson('/sharp/api/form/person/1')
            ->assertStatus(200)
            ->assertJson(["fields" => [
                "name" => [
                    "type" => "text"
                ]
            ]]);
    }

    /** @test */
    public function we_can_get_form_layout_for_an_entity()
    {
        $this->buildTheWorld();

        $this->getJson('/sharp/api/form/person/1')
            ->assertStatus(200)
            ->assertJson(["layout" => [
                "tabbed" => true,
                "tabs" => [[
                    "title" => "one",
                    "columns" => [
                        [
                            "size" => 6,
                            "fields" => [
                                [
                                    ["key" => "name"]
                                ]
                            ]
                        ]
                    ]
                ]]
            ]]);
    }

    /** @test */
    public function we_can_update_an_entity()
    {
        $this->buildTheWorld();

        $this->postJson('/sharp/api/form/person/1', [
            "name" => "Jane Fonda"
        ])->assertStatus(200)
            ->assertJson(["ok" => true]);
    }

    /** @test */
    public function we_can_delete_an_entity()
    {
        $this->buildTheWorld();

        $this->deleteJson('/sharp/api/form/person/1')
            ->assertStatus(200)
            ->assertJson(["ok" => true]);
    }

    /** @test */
    public function we_can_validate_an_entity_before_update()
    {
        $this->buildTheWorld();
        $this->configurePersonValidator();

        $this->postJson('/sharp/api/form/person/1', [
            "age" => 22
        ])->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name field is required."
                    ]
                ]
            ]);
    }

    /** @test */
    public function we_can_store_a_new_entity()
    {
        $this->buildTheWorld();

        $this->postJson('/sharp/api/form/person', [
            "name" => "Jane Fonda"
        ])->assertStatus(200)
            ->assertJson(["ok" => true]);
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

        $this->postJson('/sharp/api/form/person/notanid', [
            "name" => "Jane Fonda"
        ])->assertStatus(417)
            ->assertJson([
                "message" => "notanid is not a valid id"
            ]);
    }

    /** @test */
    public function we_can_get_form_data_for_an_entity_on_a_single_form_case()
    {
        $this->buildTheWorld(true);

        $this->getJson('/sharp/api/form/person')
            ->assertStatus(200)
            ->assertJson(["data" => [
                "name" => "Single John Wayne"
            ]]);

        $this->getJson('/sharp/api/form/person/1')->assertStatus(404);
    }

    /** @test */
    public function we_can_update_an_entity_on_a_single_form_case()
    {
        $this->buildTheWorld(true);

        $this
            ->postJson('/sharp/api/form/person', [
                "name" => "Jane Fonda"
            ])
            ->assertStatus(200)
            ->assertJson(["ok" => true]);

        $this->postJson('/sharp/api/form/person/1', [])->assertStatus(404);
    }

    /** @test */
    public function we_cant_delete_an_entity_on_a_single_form_case()
    {
        $this->buildTheWorld(true);

        $this->deleteJson('/sharp/api/form/person')
            ->assertStatus(500);

        $this->deleteJson('/sharp/api/form/person/1')->assertStatus(404);
    }

    protected function buildTheWorld($singleShow = false)
    {
        parent::buildTheWorld($singleShow);

        if($singleShow) {
            $this->app['config']->set(
                'sharp.entities.person.form',
                PersonSharpSingleForm::class
            );
        }
    }
}

class PersonSharpSingleForm extends SharpSingleForm
{

    function buildFormFields(): void
    {
        $this->addField(SharpFormTextField::make("name"));
    }

    function buildFormLayout(): void
    {
        $this->addColumn(6, function(FormLayoutColumn $column) {
            return $column->withSingleField("name");
        });
    }

    protected function findSingle()
    {
        return ["name" => "Single John Wayne", "job" => "actor"];
    }

    protected function updateSingle(array $data): void
    {
    }
}