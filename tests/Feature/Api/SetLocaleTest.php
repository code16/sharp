<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Carbon\Carbon;

class SetLocaleTest extends BaseApiTest
{
    protected function setUp()
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function we_can_ignore_locale_in_config()
    {
        $this->buildTheWorld();

        $this->assertEquals("Monday", Carbon::now()->formatLocalized("%A"));

        $this->json('get', '/sharp/api/form/person/1');

        $this->assertEquals("Monday", Carbon::now()->formatLocalized("%A"));
    }

    /** @test */
    public function we_can_set_a_locale_in_config()
    {
        $this->buildTheWorld();

        config(["sharp.locale" => "fr_FR.UTF-8"]);

        $this->assertEquals("Monday", Carbon::now()->formatLocalized("%A"));

        // Locale is set through the SetSharpLocale middleware for the request
        $this->json('get', '/sharp/api/form/person/1');

        $this->assertEquals("Lundi", Carbon::now()->formatLocalized("%A"));
    }


}