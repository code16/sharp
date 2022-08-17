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

class ProfileSingleForm extends SharpSingleForm
{
    use WithSharpFormEloquentUpdater;

    protected ?string $formValidatorClass = MyProfileValidator::class;

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormUploadField::make('avatar')
                    ->setLabel('Avatar')
                    ->setFileFilterImages()
                    ->setMaxFileSize(1)
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
                    ->withFields('name')
                    ->withFields('email');
            })
            ->addColumn(6, function (FormLayoutColumn $column) {
                $column
                    ->withSingleField('avatar');
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
        $user = auth()->user();

        $this->save($user, $data);

        return $user->id;
    }
}
