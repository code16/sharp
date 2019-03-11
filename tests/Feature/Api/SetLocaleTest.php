<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Carbon\Carbon;

class SetLocaleTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function we_can_ignore_locale_in_config()
    {
        $this->buildTheWorld();

        $this->assertEquals("monday", strtolower(Carbon::now()->startOfWeek()->formatLocalized("%A")));

        $this->json('get', '/sharp/api/form/person/1');

        $this->assertEquals("monday", strtolower(Carbon::now()->startOfWeek()->formatLocalized("%A")));
    }

    /** @test */
    public function we_can_set_a_locale_in_config()
    {
        $this->buildTheWorld();

        config(["sharp.locale" => "fr_FR.UTF-8"]);

        $this->assertEquals("monday", strtolower(Carbon::now()->startOfWeek()->formatLocalized("%A")));

        // Locale is set through the SetSharpLocale middleware for the request
        $this->json('get', '/sharp/api/form/person/1');

        $this->assertEquals("lundi", strtolower(Carbon::now()->startOfWeek()->formatLocalized("%A")));
    }


}