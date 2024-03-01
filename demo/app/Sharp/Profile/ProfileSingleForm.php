<?php

namespace App\Sharp\Profile;

use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpSingleForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\Fields\Validation\SharpImageValidation;

class ProfileSingleForm extends SharpSingleForm
{
    use WithSharpFormEloquentUpdater;

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormUploadField::make('avatar')
                    ->setLabel('Avatar')
                    ->setMaxFileSize(1)
                    ->setIsImage()
                    ->shouldOptimizeImage()
                    ->setStorageDisk('local')
                    ->setCropRatio('1:1')
                    ->setStorageBasePath('data/users/{id}'),
            )
            ->addField(
                SharpFormTextField::make('name')
                    ->setLabel('Name')
                    ->setMaxLength(300)
                    ->setHelpMessage('It will be displayed publicly'),
            )
            ->addField(
                SharpFormTextField::make('email')
                    ->setLabel('Email address')
                    ->setMaxLength(150)
                    ->setReadOnly(),
            );
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout
            ->addColumn(6, function (FormLayoutColumn $column) {
                $column
                    ->withField('name')
                    ->withField('email');
            })
            ->addColumn(6, function (FormLayoutColumn $column) {
                $column
                    ->withField('avatar');
            });
    }

    public function findSingle(): array
    {
        return $this
            ->setCustomTransformer('avatar', new SharpUploadModelFormAttributeTransformer())
            ->transform(auth()->user());
    }

    protected function updateSingle(array $data)
    {
        $this->validate(
            $data,
            ['name' => ['required', 'string', 'max:300']]
        );

        $user = auth()->user();

        $this->save($user, $data);

        return $user->id;
    }
}
