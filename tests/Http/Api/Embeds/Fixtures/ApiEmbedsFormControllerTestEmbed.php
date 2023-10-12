<?php

namespace Code16\Sharp\Tests\Http\Api\Embeds\Fixtures;

use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class ApiEmbedsFormControllerTestEmbed extends ApiEmbedsControllerTestSimpleEmbed
{
    public static string $key = 'Code16.Sharp.Tests.Http.Api.Embeds.Fixtures.ApiEmbedsFormControllerTestEmbed';

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(SharpFormTextField::make('name'))
            ->addField(SharpFormEditorField::make('bio'));
    }

    public function transformDataForFormFields(array $data): array
    {
        return $this
            ->setCustomTransformer('name', function ($value) {
                return str($value)->upper()->toString();
            })
            ->transform($data);
    }

    public function updateContent(array $data = []): array
    {
        $this->validate($data, [
            'name' => ['required'],
        ]);

        return $data;
    }
}