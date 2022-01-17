<?php

namespace App\Sharp;

use App\Feature;
use App\Pilot;
use App\Sharp\Filters\CorporationGlobalFilter;
use App\Spaceship;
use App\SpaceshipType;
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\SharpFormDateField;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTagsField;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\Layout\FormLayoutFieldset;
use Code16\Sharp\Form\Layout\FormLayoutTab;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class SpaceshipSharpForm extends SharpForm
{
    use WithSharpFormEloquentUpdater;

    protected ?string $formValidatorClass = SpaceshipSharpValidator::class;

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('name')
                    ->setLocalized()
                    ->setMaxLength(30)
                    ->setLabel('Name')
            )
            ->addField(
                SharpFormHtmlField::make('html')
                    ->setInlineTemplate('The name of the spaceship localized in FR is <strong>{{nameFr}}</strong>')
            )
            ->addField(
                SharpFormTextField::make('capacity')
                    ->setLabel('Capacity (x1000)')
            )
            ->addField(
                SharpFormEditorField::make('description')
                    ->setRenderContentAsMarkdown()
                    ->setLabel('Description')
                    ->setToolbar([
                        SharpFormEditorField::B,
                        SharpFormEditorField::I,
                        SharpFormEditorField::SEPARATOR,
                        SharpFormEditorField::UPLOAD,
                        SharpFormEditorField::SEPARATOR,
                        SharpFormEditorField::RAW_HTML,
                        SharpFormEditorField::A,
                    ])
                    ->setCropRatio('1:1')
                    ->setTransformable()
                    ->setMaxFileSize(4)
                    ->setFileFilterImages()
                    ->setStorageDisk('local')
                    ->setStorageBasePath('data/Spaceship/{id}/markdown')
                    ->setHeight(700)
            )
            ->addField(
                SharpFormDateField::make('construction_date')
                    ->setLabel('Construction date')
                    ->setDisplayFormat('YYYY/MM/DD')
                    ->setHasTime(false)
            )
            ->addField(
                SharpFormAutocompleteField::make('type_id', 'local')
                    ->setLabel('Ship type')
                    ->setAdditionalTemplateData([
                        'hasWarning' => currentSharpRequest()->isUpdate() && currentSharpRequest()->instanceId() > 10,
                    ])
                    ->setLocalSearchKeys(['label'])
                    ->setListItemTemplatePath('/sharp/templates/spaceshipType_list.vue')
                    ->setResultItemTemplatePath('/sharp/templates/spaceshipType_result.vue')
                    ->setLocalValues(
                        SpaceshipType::orderBy('label')->get()->pluck('label', 'id')->all()
                    )
            )
            ->addField(
                SharpFormSelectField::make(
                    'brand',
                    SpaceshipType::all()
                        ->mapWithKeys(function ($spaceshipType) {
                            return [
                                $spaceshipType->id => collect($spaceshipType->brands)
                                    ->mapWithKeys(function ($values, $key) {
                                        return [$key => $key];
                                    })->all(),
                            ];
                        })
                    ->all()
                )
                    ->setLabel('Brand (depends on type)')
                    ->setDisplayAsDropdown()
                    ->setOptionsLinkedTo('type_id')
            )
            ->addField(
                SharpFormUploadField::make('manual')
                    ->setLabel('Manual')
                    ->setHelpMessage('Max file size: 20 Mb')
                    ->setStorageDisk('local')
                    ->setStorageBasePath('data/Spaceship/{id}/Manual')
                    ->setFileFilter('pdf')
                    ->setMaxFileSize(20)
            )
            ->addField(
                SharpFormSelectField::make(
                    'model',
                    SpaceshipType::all()
                        ->mapWithKeys(function ($spaceshipType) {
                            return [
                                $spaceshipType->id => collect($spaceshipType->brands)
                                    ->mapWithKeys(function ($values, $key) {
                                        return [
                                            $key => collect($values)->mapWithKeys(function ($value) {
                                                return [$value => $value];
                                            })->all(),
                                        ];
                                    })->all(),
                            ];
                        })
                        ->all()
                )
                    ->setLabel('Model (depends on brand)')
                    ->setDisplayAsDropdown()
                    ->setOptionsLinkedTo('type_id', 'brand')
            )
            ->addField(
                SharpFormAutocompleteField::make('serial_number', 'remote')
                    ->setDataWrapper('data')
                    ->setLabel('S/N')
                    ->setListItemInlineTemplate('{{serial}}')
                    ->setResultItemInlineTemplate('{{serial}}')
                    ->setDynamicRemoteEndpoint('/spaceships/serial_numbers/{{type_id}}')
            )
            ->addField(
                SharpFormUploadField::make('picture')
                    ->setLabel('Picture')
                    ->setFileFilterImages()
                    ->shouldOptimizeImage()
                    ->setTransformable()
                    ->setCropRatio('1:1')
                    ->setStorageDisk('local')
                    ->setStorageBasePath('data/Spaceship/{id}')
            )
            ->addField(
                SharpFormTextField::make('picture:legend')
                    ->setLocalized()
                    ->setLabel('Legend')
            )
            ->addField(
                SharpFormTagsField::make(
                    'pilots',
                    Pilot::orderBy('name')->get()->pluck('name', 'id')->all()
                )
                    ->setLabel('Pilots')
                    ->setCreatable(true)
                    ->setCreateAttribute('name')
                    ->setMaxTagCount(4)
            )
            ->addField(
                SharpFormSelectField::make(
                    'features',
                    Feature::orderBy('name')->get()->pluck('name', 'id')->all()
                )
                    ->setLabel('Features')
                    ->setMultiple()
                    ->setDisplayAsList()
                    ->allowSelectAll()
            )
            ->addField(
                SharpFormListField::make('reviews')
                    ->setLabel('Technical reviews')
                    ->setAddable()
                    ->setRemovable()
                    ->setItemIdAttribute('id')
                    ->addItemField(
                        SharpFormDateField::make('starts_at')
                            ->setLabel('Date')
                            ->setDisplayFormat('YYYY/MM/DD HH:mm')
                            ->setMinTime(8)
                            ->setHasTime(true)
                    )
                    ->addItemField(
                        SharpFormSelectField::make('status', [
                            'ok' => 'Passed', 'ko' => 'Failed',
                        ])->setLabel('Status')
                        ->setDisplayAsList()->setInline()
                    )
                    ->addItemField(
                        SharpFormTextareaField::make('comment')
                            ->setLabel('Comment')
                            ->setMaxLength(50)
                            ->addConditionalDisplay('status', 'ko')
                    )
            )
            ->addField(
                SharpFormListField::make('pictures')
                    ->setLabel('Additional pictures')
                    ->setAddable()->setAddText('Add a picture')
                    ->allowBulkUploadForField('file')
                    ->setBulkUploadFileCountLimitAtOnce(6)
//                    ->setMaxItemCount(2)
                    ->setRemovable()
                    ->setSortable()
                    ->setItemIdAttribute('id')
                    ->setOrderAttribute('order')
                    ->addItemField(
                        SharpFormUploadField::make('file')
                            ->setFileFilterImages()
    //                        ->setCompactThumbnail()
                            ->setCropRatio('16:9')
                            ->setStorageDisk('local')
//                            ->setMaxFileSize(.5)
                            ->setStorageBasePath('data/Spaceship/{id}/Pictures')
                    )
                    ->addItemField(
                        SharpFormTextField::make('legend')
                            ->setLocalized()
                            ->setPlaceholder('Legend')
                    )
            );
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout
            ->addTab('Details', function (FormLayoutTab $tab) {
                $tab
                    ->addColumn(5, function (FormLayoutColumn $column) {
                        $column
                            ->withFieldset('Technical details', function (FormLayoutFieldset $fieldset) {
                                return $fieldset->withFields('capacity|4,6', 'construction_date|8,6');
                            })
                            ->withSingleField('features');
                    })
                    ->addColumn(7, function (FormLayoutColumn $column) {
                        $column->withSingleField('description');
                    });
            })
            ->addTab('General info', function (FormLayoutTab $tab) {
                $tab
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column
                            ->withSingleField('name')
                            ->withSingleField('html')
                            ->withSingleField('type_id')
                            ->withSingleField('serial_number')
                            ->withFields('brand|6', 'model|6')
                            ->withSingleField('manual')
                            ->withSingleField('pilots')
                            ->withSingleField('reviews', function (FormLayoutColumn $listItem) {
                                $listItem
                                    ->withSingleField('starts_at')
                                    ->withFields('status|5', 'comment|7');
                            });
                    })
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column
                            ->withSingleField('picture')
                            ->withSingleField('picture:legend')
                            ->withSingleField('pictures', function (FormLayoutColumn $listItem) {
                                $listItem
                                    ->withSingleField('file')
                                    ->withSingleField('legend');
                            });
                    });
            });
    }

    public function buildFormConfig(): void
    {
        $this->configureBreadcrumbCustomLabelAttribute('name.en')
            ->configureDisplayShowPageAfterCreation();
    }

    public function getDataLocalizations(): array
    {
        return ['fr', 'en', 'it'];
    }

    public function create(): array
    {
        return $this->transform(new Spaceship([
            'name' => [
                'en' => 'new',
                'fr' => 'nouveau',
            ],
        ]));
    }

    public function find($id): array
    {
        return $this
            ->setCustomTransformer('capacity', function ($capacity) {
                return $capacity / 1000;
            })
            ->setCustomTransformer('serial_number', function ($serial) {
                return $serial ? [
                    'id'     => $serial,
                    'serial' => str_pad($serial, 5, '0', STR_PAD_LEFT),
                ] : null;
            })
            ->setCustomTransformer('manual', new SharpUploadModelFormAttributeTransformer())
            ->setCustomTransformer('picture', new SharpUploadModelFormAttributeTransformer())
            ->setCustomTransformer('pictures', new SharpUploadModelFormAttributeTransformer())
            ->setCustomTransformer('html', function ($html, Spaceship $spaceship) {
                return [
                    'nameFr' => $spaceship->getTranslation('name', 'fr'),
                ];
            })
            ->transform(
                Spaceship::with('reviews', 'pilots', 'manual', 'picture', 'pictures', 'features')->findOrFail($id)
            );
    }

    public function update($id, array $data)
    {
        $instance = $id ? Spaceship::findOrFail($id) : new Spaceship([
            'corporation_id' => currentSharpRequest()->globalFilterFor(CorporationGlobalFilter::class),
        ]);

        if (($data['name']['fr'] ?? '') == 'error') {
            throw new SharpApplicativeException("Name can't be «error»");
        }

        $this->setCustomTransformer('capacity', function ($capacity) {
            return $capacity * 1000;
        })
            ->ignore('html')
            ->save($instance, $data);

        if (isset($data['name'])) {
            // Workaround to display this only once, in case of a double pass in this method
            // by Sharp, to handle relationships in a creation case.
            $this->notify('Spaceship was updated with success!')
                ->setDetail('Congratulations, this was not an easy thing to do.')
                ->setLevelSuccess()
                ->setAutoHide(false);
        }

        if (($data['capacity'] ?? 0) >= 1000) {
            $this->notify('this is a huge spaceship, by the way!');
        }

        return $instance->id;
    }

    public function delete($id): void
    {
        Spaceship::findOrFail($id)->delete();
    }
}
