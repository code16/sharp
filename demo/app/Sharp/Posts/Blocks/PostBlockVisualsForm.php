<?php

namespace App\Sharp\Posts\Blocks;

use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class PostBlockVisualsForm extends AbstractPostBlockForm
{
    protected static string $postBlockType = 'visuals';
    protected ?string $formValidatorClass = PostBlockVisualsValidator::class;

    public function buildFormFields(FieldsContainer $formFields): void
    {
        parent::buildFormFields($formFields);
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
                        ->setFileFilterImages()
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

    public function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout->addColumn(6, function (FormLayoutColumn $column) {
            $column->withSingleField('type')
                ->withSingleField('files', function (FormLayoutColumn $item) {
                    $item->withSingleField('file')
                        ->withSingleField('legend');
                });
        });
    }

    protected function addCustomTransformers(): self
    {
        return $this->setCustomTransformer('files', new SharpUploadModelFormAttributeTransformer());
    }
}
