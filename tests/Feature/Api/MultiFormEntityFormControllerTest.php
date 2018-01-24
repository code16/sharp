<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Illuminate\Foundation\Http\FormRequest;

class MultiFormEntityFormControllerTest extends BaseApiTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->login();
    }

    /** @test */
    public function we_can_get_form_data_for_an_sub_entity()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/form/person:small/1')
            ->assertStatus(200)
            ->assertJson(["data" => [
                "name" => "Joe Pesci"
            ]]);

        $this->json('get', '/sharp/api/form/person:big/1')
            ->assertStatus(200)
            ->assertJson(["data" => [
                "name" => "John Wayne",
                "height" => 180
            ]]);
    }

    /** @test */
    public function we_can_update_an_sub_entity()
    {
        $this->buildTheWorld();

        $this->json('post', '/sharp/api/form/person:small/1', [
            "name" => "Jane Fonda"
        ])->assertStatus(200)
            ->assertJson(["ok" => true]);

        $this->json('post', '/sharp/api/form/person:big/1', [
            "name" => "Jane Fonda"
        ])->assertStatus(200)
            ->assertJson(["ok" => true]);
    }

    /** @test */
    public function we_can_validate_a_sub_entity_before_update()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.entities.person.forms.big.validator',
            BigPersonSharpValidator::class
        );

        $this->json('post', '/sharp/api/form/person:big/1', [
            "name" => "Bob"
        ])->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "height" => [
                        "The height field is required."
                    ]
                ]
            ]);
    }

    protected function buildTheWorld()
    {
        parent::buildTheWorld();

        $this->app['config']->set('sharp.entities.person.form', null);

        $this->app['config']->set(
            'sharp.entities.person.forms', [
                "big" => [
                    "form" => BigPersonSharpForm::class,
                    "label" => "Big person"
                ], "small" => [
                    "form" => SmallPersonSharpForm::class,
                    "label" => "Small person"
                ]
            ]
        );
    }
}

class SmallPersonSharpForm extends SharpForm
{
    function buildFormFields()
    {
        $this->addField(SharpFormTextField::make("name"));
    }

    function buildFormLayout()
    {
        $this->addColumn(6, function(FormLayoutColumn $column) {
            return $column->withSingleField("name");
        });
    }

    function find($id): array
    {
        return $this
            ->transform(["name" => "Joe Pesci"]);
    }

    function update($id, array $data): bool
    {
        return true;
    }

    function delete($id): bool
    {
        return true;
    }
}

class BigPersonSharpForm extends SmallPersonSharpForm
{
    function buildFormFields()
    {
        parent::buildFormFields();
        $this->addField(SharpFormTextField::make("height"));
    }

    function find($id): array
    {
        return ["name" => "John Wayne", "height" => 180];
    }
}

class BigPersonSharpValidator extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return ['name' => 'required', "height" => "required|numeric"];
    }
}