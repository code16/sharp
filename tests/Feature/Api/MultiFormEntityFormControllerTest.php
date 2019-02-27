<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Tests\Fixtures\User;
use Illuminate\Foundation\Http\FormRequest;

class MultiFormEntityFormControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        // Policies have to be defined upfront
        $app['config']['sharp.entities.person.policy'] = AuthorizationsTestMultiPersonPolicy::class;
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

    /** @test */
    public function global_authorizations_are_applied_sub_entities()
    {
        $this->buildTheWorld();

        $this->app['config']->set('sharp.entities.person.authorizations', [
            "view" => false
        ]);

        $this->json('get', '/sharp/api/form/person:small/1')
            ->assertStatus(403);

        $this->json('get', '/sharp/api/form/person:big/1')
            ->assertStatus(403);
    }

    /** @test */
    public function policies_are_applied_to_sub_entities()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/form/person:small/3')
            ->assertStatus(403);

        $this->json('get', '/sharp/api/form/person:small/2')
            ->assertStatus(200);

        $this->json('get', '/sharp/api/form/person:big/3')
            ->assertStatus(403);
    }

    /** @test */
    public function policy_authorizations_are_appended_to_the_response_for_a_sub_entity()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/form/person:small/4')->assertJson([
            "authorizations" => [
                "delete" => false,
                "update" => false,
                "view" => true,
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

class AuthorizationsTestMultiPersonPolicy
{
    public function view(User $user, $id) { return $id != 3; }

    public function update(User $user, $id) { return $id < 3; }

    public function delete(User $user, $id) { return $id < 3; }
}