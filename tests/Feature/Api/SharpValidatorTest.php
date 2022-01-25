<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Form\Validator\SharpFormRequest;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Illuminate\Foundation\Http\FormRequest;

class SharpValidatorTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function we_can_validate_an_editor_field()
    {
        $this->buildTheWorld();
        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setValidator(ValidatorTestPersonSharpValidator::class);

        $this
            ->postJson('/sharp/api/form/person/1', [
                'name' => ['text' => ''],
            ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'name' => ['The name field is required.'], // Regular field name returned
                ],
            ]);
    }

    /** @test */
    public function we_can_validate_an_editor_localized_field()
    {
        $this->buildTheWorld();
        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setValidator(ValidatorTestPersonLocalizedSharpValidator::class);

        $this
            ->postJson('/sharp/api/form/person/1', [
                'name' => [
                    'text' => [
                        'en' => 'Something', 
                        'fr' => '',
                    ]
                ],
            ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'name.fr' => ['The name.fr field is required.'],
                ],
            ]);
    }

    /** @test */
    public function the_sharp_form_request_base_class_handles_editor_fields()
    {
        $this->buildTheWorld();
        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setValidator(ValidatorTestPersonExtendingSharpFormRequestSharpValidator::class);

        $this
            ->postJson('/sharp/api/form/person/1', [
                'name' => ['text' => ''],
            ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'name' => ['The name field is required.'], // Regular field name returned
                ],
            ]);
    }

    /** @test */
    public function the_sharp_form_request_base_class_handles_localized_editor_fields()
    {
        $this->buildTheWorld();
        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setValidator(ValidatorTestPersonLocalizedExtendingSharpFormRequestSharpValidator::class);

        $this
            ->postJson('/sharp/api/form/person/1', [
                'name' => [
                    'text' => [
                        'en' => '', 
                        'fr' => 'Something',
                    ]
                ],
            ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'name.en' => ['The name.en field is required.'],
                ],
            ]);
    }
}

class ValidatorTestPersonSharpValidator extends FormRequest
{
    public function rules()
    {
        return ['name.text' => 'required'];
    }
}

class ValidatorTestPersonLocalizedSharpValidator extends FormRequest
{
    public function rules()
    {
        return [
            'name.text.fr' => 'required',
            'name.text.en' => 'required',
        ];
    }
}

class ValidatorTestPersonExtendingSharpFormRequestSharpValidator extends SharpFormRequest
{
    public function rules()
    {
        // No need for .text because we're extending SharpFormRequest
        return ['name' => 'required'];
    }
}

class ValidatorTestPersonLocalizedExtendingSharpFormRequestSharpValidator extends SharpFormRequest
{
    public function rules()
    {
        // No need for .text because we're extending SharpFormRequest
        return [
            'name.fr' => 'required',
            'name.en' => 'required',
        ];
    }
}