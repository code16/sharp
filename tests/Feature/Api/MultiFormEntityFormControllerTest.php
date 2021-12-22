<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Auth\SharpEntityPolicy;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Tests\Unit\Utils\WithCurrentSharpRequestFake;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Foundation\Http\FormRequest;

class MultiFormEntityFormControllerTest extends BaseApiTest
{
    use WithCurrentSharpRequestFake;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->login();
        $this->buildTheWorld();
        app(SharpEntityManager::class)
            ->entityFor("person")
            ->setList(PersonWithMultiformSharpEntityList::class)
            ->setPolicy(AuthorizationsTestMultiPersonPolicy::class)
            ->setMultiforms([
                "big" => [BigPersonSharpForm::class, "Big person"],
                "small" => [SmallPersonSharpForm::class, "Small person"]
            ]);
    }

    /** @test */
    public function we_can_get_form_data_for_an_sub_entity()
    {
        $this->getJson('/sharp/api/form/person:small/1')
            ->assertStatus(200)
            ->assertJson(["data" => [
                "name" => "Joe Pesci"
            ]]);

        $this->getJson('/sharp/api/form/person:big/1')
            ->assertStatus(200)
            ->assertJson(["data" => [
                "name" => "John Wayne",
                "height" => 180
            ]]);
    }

    /** @test */
    public function we_can_update_an_sub_entity()
    {
        $this->fakeCurrentSharpRequestWithUrl("/sharp/s-list/person/s-form/person:small/1");
        $this
            ->postJson('/sharp/api/form/person:small/1', [
                "name" => "Jane Fonda"
            ])
            ->assertOk()
            ->assertJson([
                "redirectUrl" => url("/sharp/s-list/person")
            ]);

        $this->fakeCurrentSharpRequestWithUrl("/sharp/s-list/person/s-form/person:big/1");
        $this
            ->postJson('/sharp/api/form/person:big/1', [
                "name" => "Jane Fonda"
            ])
            ->assertOk()
            ->assertJson([
                "redirectUrl" => url("/sharp/s-list/person")
            ]);
    }

    /** @test */
    public function we_can_validate_a_sub_entity_before_update()
    {
        $this->app['config']->set(
            'sharp.entities.person.forms.big.validator',
            BigPersonSharpValidator::class
        );

        $this->postJson('/sharp/api/form/person:big/1', [
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
        $this->app['config']->set('sharp.entities.person.authorizations', [
            "view" => false
        ]);

        $this->getJson('/sharp/api/form/person:small/1')
            ->assertStatus(403);

        $this->getJson('/sharp/api/form/person:big/1')
            ->assertStatus(403);
    }

    /** @test */
    public function policies_are_applied_to_sub_entities()
    {
        $this->getJson('/sharp/api/form/person:small/3')
            ->assertStatus(403);

        $this->getJson('/sharp/api/form/person:small/2')
            ->assertStatus(200);

        $this->getJson('/sharp/api/form/person:big/3')
            ->assertStatus(403);
    }

    /** @test */
    public function policy_authorizations_are_appended_to_the_response_for_a_sub_entity()
    {
        $this->getJson('/sharp/api/form/person:small/4')->assertJson([
            "authorizations" => [
                "delete" => false,
                "update" => false,
                "view" => true,
            ]
        ]);
    }
}

class SmallPersonSharpForm extends SharpForm
{
    function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields->addField(SharpFormTextField::make("name"));
    }

    function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout->addColumn(6, function(FormLayoutColumn $column) {
            return $column->withSingleField("name");
        });
    }

    function find($id): array
    {
        return $this
            ->transform(["name" => "Joe Pesci"]);
    }

    function update($id, array $data)
    {
        return $id;
    }

    function delete($id): void
    {
    }
}

class BigPersonSharpForm extends SmallPersonSharpForm
{
    function buildFormFields(FieldsContainer $formFields): void
    {
        parent::buildFormFields($formFields);
        $formFields->addField(SharpFormTextField::make("height"));
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

class AuthorizationsTestMultiPersonPolicy extends SharpEntityPolicy
{
    public function view($user, $id): bool { return $id != 3; }

    public function update($user, $id): bool { return $id < 3; }

    public function delete($user, $id): bool { return $id < 3; }
}