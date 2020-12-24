<?php

namespace Code16\Sharp\Tests\Unit\Utils;

use Code16\Sharp\Http\Context\CurrentSharpRequest;
use Code16\Sharp\Tests\SharpTestCase;
use Illuminate\Support\Collection;

class CurrentSharpRequestTest extends SharpTestCase
{
    
    protected function setUp(): void
    {
        parent::setUp();
        
        app()->bind(
            CurrentSharpRequest::class, 
            function() {
                return new class extends CurrentSharpRequest {
                    public ?string $testUrl = null;
                    public function setTestUrl(string $testUrl): self
                    {
                        $this->testUrl = $testUrl;
                        return $this;
                    }
                    protected function getSegmentsFromRequest(): Collection
                    {
                        return collect(explode("/", parse_url(url($this->testUrl))["path"]))
                            ->filter(function(string $segment) {
                                return strlen(trim($segment)) && $segment !== sharp_base_url_segment();
                            })
                            ->values();
                    }
                };
            });
    }

    /** @test */
    function we_can_get_form_update_state_from_request()
    {
        /** @var CurrentSharpRequest $request */
        $request = currentSharpRequest()->setTestUrl('/sharp/s-list/person/s-show/person/1/s-form/child/2');

        $this->assertTrue($request->isForm());
        $this->assertTrue($request->isUpdate());
        $this->assertFalse($request->isCreation());
        $this->assertFalse($request->isShow());
        $this->assertFalse($request->isEntityList());
    }

    /** @test */
    function we_can_get_form_creation_state_from_request()
    {
        /** @var CurrentSharpRequest $request */
        $request = currentSharpRequest()->setTestUrl('/sharp/s-list/person/s-show/person/1/s-form/child');

        $this->assertTrue($request->isForm());
        $this->assertFalse($request->isUpdate());
        $this->assertTrue($request->isCreation());
        $this->assertFalse($request->isShow());
        $this->assertFalse($request->isEntityList());
    }

    /** @test */
    function we_can_get_show_state_from_request()
    {
        /** @var CurrentSharpRequest $request */
        $request = currentSharpRequest()->setTestUrl('/sharp/s-list/person/s-show/person/1');

        $this->assertFalse($request->isForm());
        $this->assertFalse($request->isUpdate());
        $this->assertFalse($request->isCreation());
        $this->assertTrue($request->isShow());
        $this->assertFalse($request->isEntityList());
    }

    /** @test */
    function we_can_get_entity_list_state_from_request()
    {
        /** @var CurrentSharpRequest $request */
        $request = currentSharpRequest()->setTestUrl('/sharp/s-list/person');

        $this->assertFalse($request->isForm());
        $this->assertFalse($request->isUpdate());
        $this->assertFalse($request->isCreation());
        $this->assertFalse($request->isShow());
        $this->assertTrue($request->isEntityList());
    }

    /** @test */
    function we_can_get_current_breadcrumb_item_from_request()
    {
        /** @var CurrentSharpRequest $request */
        $request = currentSharpRequest()->setTestUrl('/sharp/s-list/person/s-show/person/1/s-form/child/2');
        
        $this->assertTrue($request->getCurrentBreadcrumbItem()->isForm());
        $this->assertFalse($request->getCurrentBreadcrumbItem()->isSingleForm());
        $this->assertEquals("child", $request->getCurrentBreadcrumbItem()->entityKey());
        $this->assertEquals(2, $request->getCurrentBreadcrumbItem()->instanceId());
    }

    /** @test */
    function we_can_get_previous_show_from_request()
    {
        /** @var CurrentSharpRequest $request */
        $request = currentSharpRequest()->setTestUrl('/sharp/s-list/person/s-show/person/42/s-form/child/2');

        $this->assertEquals("person", $request->getPreviousShowFromBreadcrumbItems()->entityKey());
        $this->assertEquals(42, $request->getPreviousShowFromBreadcrumbItems()->instanceId());
    }

    /** @test */
    function we_can_get_previous_show_of_a_given_key_from_request()
    {
        /** @var CurrentSharpRequest $request */
        $request = currentSharpRequest()->setTestUrl('/sharp/s-list/person/s-show/person/31/s-show/person/42/s-show/child/84/s-form/child/84');

        $this->assertEquals("child", $request->getPreviousShowFromBreadcrumbItems()->entityKey());
        $this->assertEquals(84, $request->getPreviousShowFromBreadcrumbItems()->instanceId());
        $this->assertEquals("person", $request->getPreviousShowFromBreadcrumbItems("person")->entityKey());
        $this->assertEquals(42, $request->getPreviousShowFromBreadcrumbItems("person")->instanceId());
    }
}