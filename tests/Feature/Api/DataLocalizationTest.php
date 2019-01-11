<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Fixtures\PersonSharpForm;

class DataLocalizationTest extends BaseApiTest
{
    protected function setUp()
    {
        parent::setUp();

        $this->app['config']->set(
            'app.key', 'base64:'.base64_encode(random_bytes(
                $this->app['config']['app.cipher'] == 'AES-128-CBC' ? 16 : 32
            ))
        );

        $this->login();
    }

    /** @test */
    function we_add_the_locales_array_if_configured_to_the_form()
    {
        $this->app['config']->set(
            'sharp.entities.person.form',
            DataLocalizationTestForm::class
        );

        $this->json('get', '/sharp/api/form/person')
            ->assertJson(["locales" => ["fr", "en"]]);

        $this->json('get', '/sharp/api/form/person/50')
            ->assertJson(["locales" => ["fr", "en"]]);
    }

    /** @test */
    function we_wont_add_the_locales_array_if_not_configured()
    {
        $this->app['config']->set(
            'sharp.entities.person.form',
            PersonSharpForm::class
        );

        $this->json('get', '/sharp/api/form/person')
            ->assertJsonMissing(["locales"]);

        $this->json('get', '/sharp/api/form/person/50')
            ->assertJsonMissing(["locales"]);
    }

    /** @test */
    function we_wont_add_the_locales_array_if_configured_but_there_is_no_localized_field()
    {
        $this->app['config']->set(
            'sharp.entities.person.form',
            DataLocalizationWithoutLocalizedFieldTestForm::class
        );

        $this->json('get', '/sharp/api/form/person')
            ->assertJsonMissing(["locales"]);

        $this->json('get', '/sharp/api/form/person/50')
            ->assertJsonMissing(["locales"]);
    }
}

class DataLocalizationTestForm extends PersonSharpForm
{
    function buildFormFields()
    {
        $this->addField(SharpFormTextField::make("name")->setLocalized());
    }

    function getDataLocalizations()
    {
        return ["fr", "en"];
    }
}

class DataLocalizationWithoutLocalizedFieldTestForm extends PersonSharpForm
{

    function getDataLocalizations()
    {
        return ["fr", "en"];
    }
}