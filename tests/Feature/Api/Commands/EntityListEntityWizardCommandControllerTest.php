<?php

namespace Code16\Sharp\Tests\Feature\Api\Commands;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\Wizards\EntityWizardCommand;
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Feature\Api\BaseApiTest;
use Code16\Sharp\Tests\Fixtures\PersonEntity;
use Code16\Sharp\Tests\Fixtures\PersonSharpEntityList;
use Code16\Sharp\Tests\Fixtures\SinglePersonEntity;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EntityListEntityWizardCommandControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->login();
        $this->app['config']->set(
            'sharp.entities.person.list',
            EntityWizardCommandTestPersonSharpEntityList::class
        );
    }

    /** @test */
    public function we_get_first_step_form_on_the_first_call_of_a_wizard_entity_command()
    {
        $this->withoutExceptionHandling();

        $this
            ->getJson('/sharp/api/list/person/command/entity_wizard/form')
            ->assertOk()
            ->assertJsonFragment([
                'fields' => [
                    "name" => [
                        "key" => "name",
                        "type" => "text",
                        "inputType" => "text",
                    ]
                ]
            ]);
    }

    /** @test */
    public function we_can_post_first_step_of_a_wizard_entity_command()
    {
        $this
            ->postJson('/sharp/api/list/person/command/entity_wizard')
            ->assertJsonValidationErrors("name");
        
        $this
            ->postJson(
                '/sharp/api/list/person/command/entity_wizard', 
                [
                    'data' => ['name' => 'test']
                ]
            )
            ->assertOk()
            ->assertJson([
                'action' => 'step',
                'step' => "number-2:test-key" // test-key is set for the whole test
            ]);
    }

    /** @test */
    public function we_can_get_second_step_of_a_wizard_entity_command_only_with_valid_context()
    {
        // Try to get step 2 first: error (SharpInvalidStepException)
        $this
            ->getJson('/sharp/api/list/person/command/entity_wizard/form?command_step=number-2:test-key')
            ->assertStatus(500);

        // First post step 1...
        $this
            ->postJson(
                '/sharp/api/list/person/command/entity_wizard',
                [
                    'data' => ['name' => 'test']
                ]
            );
        
        // Then get step 2
        $this
            ->getJson('/sharp/api/list/person/command/entity_wizard/form?command_step=number-2:test-key')
            ->assertOk()
            ->assertJsonFragment([
                'fields' => [
                    "age" => [
                        "key" => "age",
                        "type" => "text",
                        "inputType" => "text",
                    ]
                ]
            ])
            ->assertJsonFragment([
                'data' => [
                    'age' => today()->day
                ]
            ]);
    }

    /** @test */
    public function we_can_post_second_step_of_a_wizard_entity_command()
    {
        $this
            ->postJson(
                '/sharp/api/list/person/command/entity_wizard',
                [
                    'command_step' => 'number-2:test-key',
                    'data' => ['age' => 22],
                ]
            )
            ->assertOk()
            ->assertJson([
                'action' => 'info',
                'message' => "ok"
            ]);
    }

    /** @test */
    public function we_can_define_step_execute_in_the_global_method()
    {
        $this
            ->postJson(
                '/sharp/api/list/person/command/entity_wizard',
                [
                    'command_step' => 'number-3:test-key',
                ]
            )
            ->assertOk()
            ->assertJson([
                'action' => 'info',
                'message' => "ok step 3"
            ]);
    }
}

class EntityWizardCommandTestPersonSharpEntityList extends PersonSharpEntityList
{
    public function getEntityCommands(): ?array
    {
        return [
            'entity_wizard' => new class() extends EntityWizardCommand
            {
                protected function getKey(): string
                {
                    return 'test-key';
                }

                public function label(): string
                {
                    return 'label';
                }

                public function buildFormFieldsForFirstStep(FieldsContainer $formFields): void
                {
                    $formFields->addField(SharpFormTextField::make('name'));
                }

                public function initialDataForStepNumber2(): array
                {
                    $this->getWizardContext()->validate(['first-step-passed' => 'required']);
                    
                    return [
                        'age' => today()->day
                    ];
                }

                public function buildFormFieldsForStepNumber2(FieldsContainer $formFields): void
                {
                    $formFields->addField(SharpFormTextField::make('age'));
                }

                protected function executeFirstStep(array $data): array
                {
                    $this->validate($data, ["name" => "required"]);
                    
                    $this->getWizardContext()->put('first-step-passed', true);
                    
                    return $this->toStep("number-2");
                }

                protected function executeStepNumber2(array $data): array
                {
                    $this->validate($data, ["age" => "required"]);

                    return $this->info("ok");
                }
                
                public function executeStep(string $step, array $data = []): array
                {
                    if($step === "number-3") {
                        return $this->info("ok step 3");
                    }
                    
                    throw new SharpApplicativeException();
                }
            },
        ];
    }
}
