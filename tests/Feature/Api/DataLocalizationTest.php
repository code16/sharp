<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Tests\Fixtures\PersonSharpForm;
use Code16\Sharp\Tests\Fixtures\PersonSharpShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class DataLocalizationTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function we_add_the_locales_array_if_configured_to_the_form()
    {
        $this->app['config']->set(
            'sharp.entities.person.form',
            DataLocalizationTestForm::class,
        );

        $this->getJson('/sharp/api/form/person')
            ->assertJson(['locales' => ['fr', 'en']]);

        $this->getJson('/sharp/api/form/person/50')
            ->assertJson(['locales' => ['fr', 'en']]);
    }

    /** @test */
    public function we_wont_add_the_locales_array_if_not_configured()
    {
        $this->app['config']->set(
            'sharp.entities.person.form',
            PersonSharpForm::class,
        );

        $this->getJson('/sharp/api/form/person')
            ->assertJsonMissing(['locales']);

        $this->getJson('/sharp/api/form/person/50')
            ->assertJsonMissing(['locales']);
    }

    /** @test */
    public function we_wont_add_the_locales_array_if_configured_but_there_is_no_localized_field()
    {
        $this->app['config']->set(
            'sharp.entities.person.form',
            DataLocalizationWithoutLocalizedFieldTestForm::class,
        );

        $this->getJson('/sharp/api/form/person')
            ->assertJsonMissing(['locales']);

        $this->getJson('/sharp/api/form/person/50')
            ->assertJsonMissing(['locales']);
    }

    /** @test */
    public function we_add_the_locales_array_if_configured_in_a_form_list_field()
    {
        $this->app['config']->set(
            'sharp.entities.person.form',
            DataLocalizationWithLocalizedFormListTestForm::class,
        );

        $this->getJson('/sharp/api/form/person')
            ->assertJson(['locales' => ['fr', 'en']]);

        $this->getJson('/sharp/api/form/person/50')
            ->assertJson(['locales' => ['fr', 'en']]);
    }

    /** @test */
    public function we_add_the_locales_array_if_configured_to_the_show()
    {
        $this->app['config']->set(
            'sharp.entities.person.show',
            DataLocalizationTestShow::class,
        );

        $this->getJson('/sharp/api/show/person/50')
            ->assertJson(['locales' => ['fr', 'en']]);
    }
}

class DataLocalizationTestForm extends PersonSharpForm
{
    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields->addField(SharpFormTextField::make('name')->setLocalized());
    }

    public function getDataLocalizations(): array
    {
        return ['fr', 'en'];
    }
}

class DataLocalizationWithoutLocalizedFieldTestForm extends PersonSharpForm
{
    public function getDataLocalizations(): array
    {
        return ['fr', 'en'];
    }
}

class DataLocalizationWithLocalizedFormListTestForm extends PersonSharpForm
{
    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields->addField(
            SharpFormListField::make('name')
                ->addItemField(
                    SharpFormTextField::make('name')->setLocalized(),
                ),
        );
    }

    public function getDataLocalizations(): array
    {
        return ['fr', 'en'];
    }
}

class DataLocalizationTestShow extends PersonSharpShow
{
    public function buildShowFields(FieldsContainer $showFields): void
    {
        $showFields->addField(SharpShowTextField::make('name')->setLocalized());
    }

    public function getDataLocalizations(): array
    {
        return ['fr', 'en'];
    }
}
