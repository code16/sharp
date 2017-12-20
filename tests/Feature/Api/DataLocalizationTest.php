<?php

namespace Code16\Sharp\Tests\Feature\Api;

class DataLocalizationTest extends BaseApiTest
{

    /** @test */
    function we_add_the_locales_array_if_configured_to_the_form()
    {
        $this->buildTheWorld();
        $this->login();

        $this->app['config']->set(
            'sharp.locales', ["fr", "en"]
        );

        $this->json('get', '/sharp/api/form/person')
            ->assertJson(["locales" => ["fr", "en"]]);

        $this->json('get', '/sharp/api/form/person/50')
            ->assertJson(["locales" => ["fr", "en"]]);
    }

    /** @test */
    function we_wont_add_the_locales_array_if_not_configured()
    {
        $this->buildTheWorld();
        $this->login();

        $this->json('get', '/sharp/api/form/person')
            ->assertJsonMissing(["locales"]);

        $this->json('get', '/sharp/api/form/person/50')
            ->assertJsonMissing(["locales"]);
    }
}