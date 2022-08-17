<?php

namespace Code16\Sharp\Tests\Unit\Utils;

use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Utils\Entities\SharpEntity;

class CurrentSharpRequestTest extends SharpTestCase
{
    use WithCurrentSharpRequestFake;

    /** @test */
    public function we_can_get_form_update_state_from_request()
    {
        $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person/s-show/person/1/s-form/child/2');

        $this->assertTrue(currentSharpRequest()->isForm());
        $this->assertTrue(currentSharpRequest()->isUpdate());
        $this->assertFalse(currentSharpRequest()->isCreation());
        $this->assertFalse(currentSharpRequest()->isShow());
        $this->assertFalse(currentSharpRequest()->isEntityList());
    }

    /** @test */
    public function we_can_get_form_creation_state_from_request()
    {
        // We have to define "child" as a non-single form
        $this->app->bind('child_entity', function () {
            return new class extends SharpEntity {};
        });
        $this->app['config']->set('sharp.entities.child', 'child_entity');

        $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person/s-show/person/1/s-form/child');

        $this->assertTrue(currentSharpRequest()->isForm());
        $this->assertFalse(currentSharpRequest()->isUpdate());
        $this->assertTrue(currentSharpRequest()->isCreation());
        $this->assertFalse(currentSharpRequest()->isShow());
        $this->assertFalse(currentSharpRequest()->isEntityList());
    }

    /** @test */
    public function we_can_get_show_state_from_request()
    {
        $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person/s-show/person/1');

        $this->assertFalse(currentSharpRequest()->isForm());
        $this->assertFalse(currentSharpRequest()->isUpdate());
        $this->assertFalse(currentSharpRequest()->isCreation());
        $this->assertTrue(currentSharpRequest()->isShow());
        $this->assertFalse(currentSharpRequest()->isEntityList());
    }

    /** @test */
    public function we_can_get_entity_list_state_from_request()
    {
        $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person');

        $this->assertFalse(currentSharpRequest()->isForm());
        $this->assertFalse(currentSharpRequest()->isUpdate());
        $this->assertFalse(currentSharpRequest()->isCreation());
        $this->assertFalse(currentSharpRequest()->isShow());
        $this->assertTrue(currentSharpRequest()->isEntityList());
    }

    /** @test */
    public function we_can_get_current_breadcrumb_item_from_request()
    {
        $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person/s-show/person/1/s-form/child/2');

        $this->assertTrue(currentSharpRequest()->getCurrentBreadcrumbItem()->isForm());
        $this->assertFalse(currentSharpRequest()->getCurrentBreadcrumbItem()->isSingleForm());
        $this->assertEquals('child', currentSharpRequest()->getCurrentBreadcrumbItem()->entityKey());
        $this->assertEquals(2, currentSharpRequest()->getCurrentBreadcrumbItem()->instanceId());
    }

    /** @test */
    public function we_can_get_previous_show_from_request()
    {
        $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person/s-show/person/42/s-form/child/2');

        $this->assertEquals('person', currentSharpRequest()->getPreviousShowFromBreadcrumbItems()->entityKey());
        $this->assertEquals(42, currentSharpRequest()->getPreviousShowFromBreadcrumbItems()->instanceId());
    }

    /** @test */
    public function we_can_get_previous_show_of_a_given_key_from_request()
    {
        $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person/s-show/person/31/s-show/person/42/s-show/child/84/s-form/child/84');

        $this->assertEquals('child', currentSharpRequest()->getPreviousShowFromBreadcrumbItems()->entityKey());
        $this->assertEquals(84, currentSharpRequest()->getPreviousShowFromBreadcrumbItems()->instanceId());
        $this->assertEquals('person', currentSharpRequest()->getPreviousShowFromBreadcrumbItems('person')->entityKey());
        $this->assertEquals(42, currentSharpRequest()->getPreviousShowFromBreadcrumbItems('person')->instanceId());
    }

    /** @test */
    public function we_can_get_previous_url_from_request()
    {
        $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person/s-show/person/42/s-form/child/2');

        $this->assertEquals(url('/sharp/s-list/person/s-show/person/42'), currentSharpRequest()->getUrlOfPreviousBreadcrumbItem());
    }
}
