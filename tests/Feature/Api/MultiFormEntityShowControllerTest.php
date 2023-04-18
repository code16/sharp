<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class MultiFormEntityShowControllerTest extends BaseApiTest
{
    /** @test */
    public function we_get_the_multiform_attribute_valuated_on_a_multiform_entity()
    {
        $this->withoutExceptionHandling();
        $this->login();
        $this->buildTheWorld();

        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setShow(PersonWithMultiformSharpShow::class)
            ->setMultiforms([
                'big' => [BigPersonSharpForm::class, 'Big person'],
                'small' => [SmallPersonSharpForm::class, 'Small person'],
            ]);

        $this->getJson('/sharp/api/show/person/1')
            ->assertOk()
            ->assertJson([
                'config' => [
                    'multiformAttribute' => 'type'
                ],
                'data' => [
                    'name' => 'John Wayne',
                    'type' => 'small',
                ]
            ]);
    }
}

class PersonWithMultiformSharpShow extends SharpShow
{
    public function buildShowConfig(): void
    {
        $this->configureMultiformAttribute('type');
    }

    protected function find(mixed $id): array
    {
        return $this
            ->setCustomTransformer('type', function () {
                return 'small';
            })
            ->transform([
                'id' => 1, 
                'name' => 'John Wayne',
            ]);
    }

    protected function buildShowFields(FieldsContainer $showFields): void
    {
        $showFields->addField(
            SharpShowTextField::make('name'),
        );
    }

    protected function buildShowLayout(ShowLayout $showLayout): void
    {
        $showLayout->addSection('main', function ($section) {
            $section->addColumn(6, function ($column) {
                $column->withSingleField('name');
            });
        });
    }
}
