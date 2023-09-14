<?php

use Code16\Sharp\Utils\Entities\SharpEntityManager;

it('we_can_get_list_data_for_an_entity', function ()  {
    $this->json('get', '/sharp/api/list/person')
        ->assertOk()
        ->assertJson([
            'data' => [
                'list' => [
                    'items' => [
                        ['id' => 1, 'name' => 'John <b>Wayne</b>', 'age' => 22],
                        ['id' => 2, 'name' => 'Mary <b>Wayne</b>', 'age' => 26],
                    ],
                ],
            ],
        ]);
})->todo();

it('we_can_get_paginated_list_data_for_an_entity', function ()  {
    $this->json('get', '/sharp/api/list/person?paginated=1')
        ->assertOk()
        ->assertJsonFragment(['data' => [
            'list' => [
                'items' => [
                    ['id' => 1, 'name' => 'John <b>Wayne</b>', 'age' => 22],
                    ['id' => 2, 'name' => 'Mary <b>Wayne</b>', 'age' => 26],
                ],
                'page' => 1,
                'totalCount' => 20,
                'pageSize' => 2,
            ],
        ]]);
})->todo();

it('we_can_search_for_an_instance', function ()  {
    $this->withoutExceptionHandling();
    $this->json('get', '/sharp/api/list/person?search=john')
        ->assertOk()
        ->assertJsonFragment([
            'data' => [
                'list' => [
                    'items' => [
                        ['id' => 1, 'name' => 'John <b>Wayne</b>', 'age' => 22],
                    ],
                ],
            ],
        ]);
})->todo();

it('we_wont_get_entity_attribute_for_a_non_form_data', function ()  {
    $result = $this->json('get', '/sharp/api/list/person');

    $this->assertArrayNotHasKey('job', $result->json()['data']['list']['items'][0]);
})->todo();

it('we_can_get_data_containers_for_an_entity', function ()  {
    $this->json('get', '/sharp/api/list/person')
        ->assertOk()
        ->assertJson(['containers' => [
            'name' => [
                'key' => 'name',
                'label' => 'Name',
                'sortable' => true,
                'html' => true,
            ], 'age' => [
                'key' => 'age',
                'label' => 'Age',
                'sortable' => true,
            ],
        ]]);
})->todo();

it('we_can_get_list_layout_for_an_entity', function ()  {
    $this->getJson('/sharp/api/list/person')
        ->assertOk()
        ->assertJson(['layout' => [
            [
                'key' => 'name',
                'size' => 6,
                'sizeXS' => 'fill',
                'hideOnXS' => false,
            ], [
                'key' => 'age',
                'size' => 6,
                'sizeXS' => 6,
                'hideOnXS' => true,
            ],
        ]]);
})->todo();

it('we_can_get_list_config_for_an_entity', function ()  {
    $this->json('get', '/sharp/api/list/person')
        ->assertOk()
        ->assertJson(['config' => [
            'instanceIdAttribute' => 'id',
            'searchable' => true,
            'paginated' => false,
        ]]);
})->todo();

it('we_can_get_notifications', function ()  {
    (new PersonSharpForm())->notify('title')
        ->setLevelSuccess()
        ->setDetail('body')
        ->setAutoHide(false);

    $this->json('get', '/sharp/api/list/person')
        ->assertOk()
        ->assertJson(['notifications' => [[
            'level' => 'success',
            'title' => 'title',
            'message' => 'body',
            'autoHide' => false,
        ]]]);

    $this->json('get', '/sharp/api/list/person')
        ->assertOk()
        ->assertJsonMissing(['alert']);

    (new PersonSharpForm())->notify('title1');
    (new PersonSharpForm())->notify('title2');

    $this->json('get', '/sharp/api/list/person')
        ->assertOk()
        ->assertJson(['notifications' => [[
            'title' => 'title1',
        ], [
            'title' => 'title2',
        ]]]);
})->todo();

it('invalid_entity_key_is_returned_as_404', function ()  {
    $this->getJson('/sharp/api/list/notanvalidentity')
        ->assertStatus(404);
})->todo();

it('we_can_reorder_instances', function ()  {
    $this->withoutExceptionHandling();

    $this
        ->postJson('/sharp/api/list/person/reorder', [
            'instances' => [3, 2, 1],
        ])
        ->assertOk();
})->todo();

it('list_config_contains_hasShowPage_is_relevant', function ()  {
    $this->getJson('/sharp/api/list/person')
        ->assertOk()
        ->assertJson(['config' => [
            'hasShowPage' => true,
        ]]);

    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setShow(null);

    $this->getJson('/sharp/api/list/person')
        ->assertOk()
        ->assertJson(['config' => [
            'hasShowPage' => false,
        ]]);
})->todo();

it('we_can_delete_an_instance_in_the_entity_list_if_delete_method_is_implemented', function ()  {
    $this->withoutExceptionHandling();
    $this->buildTheWorld();

    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setList(PersonSharpEntityListWithDeletion::class);

    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setShow(PersonSharpShowWithoutDeletion::class);

    $this->deleteJson('/sharp/api/list/person/1')
        ->assertOk()
        ->assertJson([
            'ok' => true,
        ]);
})->todo();

it('we_delegate_deletion_to_the_show_page_if_exists', function ()  {
    $this->withoutExceptionHandling();
    $this->buildTheWorld();

    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setList(PersonSharpEntityListWithoutDeletion::class);

    $this->deleteJson('/sharp/api/list/person/1')
        ->assertOk()
        ->assertJson([
            'ok' => true,
        ]);
})->todo();

it('as_a_legacy_workaround_we_delegate_deletion_to_the_form_page_if_exists', function ()  {
    $this->withoutExceptionHandling();
    $this->buildTheWorld();

    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setList(PersonSharpEntityListWithoutDeletion::class);

    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setShow(null);

    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setForm(PersonSharpFormWithDeletion::class);

    $this->deleteJson('/sharp/api/list/person/1')
        ->assertOk()
        ->assertJson([
            'ok' => true,
        ]);
})->todo();

it('we_can_not_delete_an_instance_in_the_entity_list_without_authorization', function ()  {
    $this->buildTheWorld();

    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setList(PersonSharpEntityListWithDeletion::class);

    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setProhibitedActions(['delete']);

    $this->deleteJson('/sharp/api/list/person/1')
        ->assertForbidden();
})->todo();

it('we_throw_an_exception_if_delete_is_not_implemented_and_there_is_no_show', function ()  {
    $this->buildTheWorld();

    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setList(PersonSharpEntityListWithoutDeletion::class);

    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setShow(null);

    $this->deleteJson('/sharp/api/list/person/1')
        ->assertStatus(500);
})->todo();

//class PersonSharpEntityListWithDeletion extends PersonSharpEntityList
//{
//public function delete(mixed $id): void
//{
//}
//}
//
//// Just an empty list impl to be sure we don't call delete on it
//class PersonSharpEntityListWithoutDeletion extends SharpEntityList
//{
//public function getListData(): array|Arrayable
//{
//    return [];
//}
//}
//
//class PersonSharpShowWithoutDeletion extends PersonSharpShow
//{
//public function delete(mixed $id): void
//{
//    throw new Exception('Should not be called');
//}
//}
//
//class PersonSharpFormWithDeletion extends PersonSharpForm
//{
//public function delete(mixed $id): void
//{
//}
//}
