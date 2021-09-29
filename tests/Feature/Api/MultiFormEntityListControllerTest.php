<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Tests\Fixtures\PersonSharpForm;
use Illuminate\Contracts\Support\Arrayable;

class MultiFormEntityListControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    /** @test */
    public function we_get_the_forms_attributes_on_a_multiform_entity()
    {
        $this->withoutExceptionHandling();
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/list/person')
            ->assertStatus(200)
            ->assertJson(["forms" => [
                "big" => [
                    "label" => "Big person",
                    "instances" => [2]
                ],
                "small" => [
                    "label" => "Small person",
                    "instances" => [1]
                ]
            ]]);
    }

    protected function buildTheWorld($singleShow = false)
    {
        $this->app['config']->set(
            'sharp.entities.person.list',
            PersonWithMultiformSharpEntityList::class
        );

        $this->app['config']->set(
            'sharp.entities.person.forms', [
                "big" => [
                    "form" => PersonSharpForm::class,
                    "label" => "Big person"
                ], 
                "small" => [
                    "form" => PersonSharpForm::class,
                    "label" => "Small person"
                ]
            ]
        );

        $this->app['config']->set(
            'app.key', 'base64:'.base64_encode(random_bytes(
                $this->app['config']['app.cipher'] == 'AES-128-CBC' ? 16 : 32
            ))
        );
    }
}

class PersonWithMultiformSharpEntityList extends SharpEntityList
{
    function getListData(): array|Arrayable
    {
        return $this
            ->setCustomTransformer("type", function($a, $person) {
                return $person['id']%2 == 0 ? "big" : "small";
            })
            ->transform([
                ["id" => 1, "name" => "John Wayne"],
                ["id" => 2, "name" => "Mary Wayne"],
            ]);
    }

    function buildListDataContainers(): void
    {
        $this->addDataContainer(
            EntityListDataContainer::make("name")
        );
    }

    function buildListLayout(): void
    {
        $this->addColumn("name", 12);
    }

    function buildListConfig(): void
    {
        $this->setMultiformAttribute("type");
    }
}