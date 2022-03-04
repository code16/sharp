<?php

namespace Code16\Sharp\Tests\Feature;

use Code16\Sharp\Tests\Feature\Api\BaseApiTest;

class CustomUrlSegmentTest extends BaseApiTest
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        app()['config']->set(
            'sharp.custom_url_segment',
            'test'
        );
    }

    /** @test */
    public function we_can_define_a_custom_url_segment_for_http_routes()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this->get(route('code16.sharp.login'))->assertOk();
        $this->assertEquals($this->baseUrl.'/test/login', request()->url());

        $this->login();

        $this->getJson(route('code16.sharp.api.list', 'person'))->assertOk();
        $this->assertEquals($this->baseUrl.'/test/api/list/person', request()->url());
    }
}
