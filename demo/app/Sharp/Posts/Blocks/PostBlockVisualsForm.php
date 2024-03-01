<?php

namespace App\Sharp\Posts\Blocks;

use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\Fields\Validation\SharpImageValidation;

class PostBlockVisualsForm extends AbstractPostBlockForm
{
    protected static string $postBlockType = 'visuals';
    protected ?string $formValidatorClass = PostBlockVisualsValidator::class;

    protected function getContentField(): ?SharpFormField
    {
        return null;
    }

    protected function buildAdditionalFields(FieldsContainer $formFields): void
    {
        $formFields->addField(
            SharpFormListField::make('files')
                ->setLabel('Visuals')
                ->setAddable()->setAddText('Add a visual')
                ->setRemovable()
                ->setMaxItemCount(5)
                ->setSortable()->setOrderAttribute('order')
                ->allowBulkUploadForField('file')
                ->addItemField(
                    SharpFormUploadField::make('file')
                        ->setMaxFileSize(1)
                        ->setStorageDisk('local')
                        ->setStorageBasePath(function () {
                            return sprintf(
                                'data/posts/%s/blocks/{id}',
                                currentSharpRequest()->getPreviousShowFromBreadcrumbItems('posts')->instanceId(),
                            );
                        }),
                )
                ->addItemField(
                    SharpFormTextField::make('legend')
                        ->setPlaceholder('Legend'),
                ),
        );
    }

    protected function addAdditionalFieldsToLayout(FormLayoutColumn $column): void
    {
        $column
            ->withListField('files', function (FormLayoutColumn $item) {
                $item->withField('file')
                    ->withField('legend');
            });
    }

    protected function addCustomTransformers(): self
    {
        return $this->setCustomTransformer('files', new SharpUploadModelFormAttributeTransformer());
    }
}
