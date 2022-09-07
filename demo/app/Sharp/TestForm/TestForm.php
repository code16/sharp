<?php

namespace App\Sharp\TestForm;

use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteListField;
use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Code16\Sharp\Form\Fields\SharpFormDateField;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormGeolocationField;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormNumberField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTagsField;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\Layout\FormLayoutTab;
use Code16\Sharp\Form\SharpSingleForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class TestForm extends SharpSingleForm
{
    protected ?string $formValidatorClass = TestValidator::class;

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('text')
                    ->setLocalized()
                    ->setLabel('Text'),
            )
            ->addField(
                SharpFormAutocompleteField::make('autocomplete_local', 'local')
                    ->setLocalized()
                    ->setLabel('Autocomplete local')
                    ->setLocalSearchKeys(['label'])
                    ->setListItemInlineTemplate('{{label}}')
                    ->setResultItemInlineTemplate('{{label}} ({{id}})')
                    ->setLocalValues($this->options(true)),
            )
            ->addField(
                SharpFormAutocompleteField::make('autocomplete_remote', 'remote')
                    ->setLabel('Autocomplete remote')
                    ->setRemoteSearchAttribute('query')
                    ->setListItemInlineTemplate('{{name}}')
                    ->setResultItemInlineTemplate('{{name}} ({{num}})')
                    ->setRemoteEndpoint(url('/passengers')),
            )
            ->addField(
                SharpFormAutocompleteListField::make('autocomplete_list')
                    ->setLabel('Autocomplete_list')
                    ->setAddable()
                    ->setRemovable()
                    ->setItemField(
                        SharpFormAutocompleteField::make('item', 'remote')
                            ->setLabel('Passenger')
                            ->setPlaceholder('test')
                            ->setListItemInlineTemplate('{{ name }}')
                            ->setResultItemInlineTemplate('{{name}} ({{num}})')
                            ->setRemoteEndpoint(url('/passengers')),
                    ),
            )
            ->addField(
                SharpFormCheckField::make('check', 'Check'),
            )
            ->addField(
                SharpFormDateField::make('date')
                    ->setLabel('Date')
                    ->setDisplayFormat('YYYY-MM-DD HH:mm')
                    ->setMinTime(8)
                    ->setMaxTime(16)
                    ->setStepTime(15)
                    ->setHasTime(true),
            )
            ->addField(
                SharpFormGeolocationField::make('geolocation')
                    ->setLabel('Geolocation')
                    ->setApiKey(env('GMAPS_KEY'))
                    ->setMapsProvider('osm')
                    ->setGeocodingProvider('osm')
    //                ->setDisplayUnitDecimalDegrees()
                    ->setDisplayUnitDegreesMinutesSeconds()
                    ->setGeocoding()
                    ->setInitialPosition(48.5838961, 7.742182599999978),
            )
            ->addField(
                SharpFormHtmlField::make('html')
                    ->setLabel('Html')
                    ->setInlineTemplate('Your name is <strong>{{name}}</strong>'),
            )
            ->addField(
                SharpFormListField::make('list')
                    ->setLabel('List')
                    ->setAddable()
                    ->setSortable()
                    ->setRemovable()
                    ->setItemIdAttribute('id')
                    ->addItemField(
                        SharpFormDateField::make('date')
                            ->setLabel('Date')
                            ->setDisplayFormat('YYYY/MM/DD')
                            ->setHasTime(false),
                    )
                    ->addItemField(
                        SharpFormCheckField::make('check', 'check this'),
                    )
                    ->addItemField(SharpFormEditorField::make('markdown2')
                        ->setLocalized()
                        ->setLabel('Markdown')
                        ->setToolbar([
                            SharpFormEditorField::B, SharpFormEditorField::I, SharpFormEditorField::A,
                        ]),
                    ),
            )
            ->addField(
                SharpFormEditorField::make('markdown')
                    ->setRenderContentAsMarkdown()
                    ->setLocalized()
                    ->setLabel('Markdown')
                    ->setToolbar([
                        SharpFormEditorField::B,
                        SharpFormEditorField::I,
                        SharpFormEditorField::A,
                        SharpFormEditorField::SEPARATOR,
                        SharpFormEditorField::H1,
                        SharpFormEditorField::H2,
                        SharpFormEditorField::H3,
                        SharpFormEditorField::HR,
                        SharpFormEditorField::SEPARATOR,
                        SharpFormEditorField::OL,
                        SharpFormEditorField::UL,
                        SharpFormEditorField::QUOTE,
                        SharpFormEditorField::CODE,
                        SharpFormEditorField::UPLOAD_IMAGE,
                        SharpFormEditorField::SEPARATOR,
                        SharpFormEditorField::TABLE,
                        SharpFormEditorField::IFRAME,
                        SharpFormEditorField::RAW_HTML,
                        SharpFormEditorField::SEPARATOR,
                        SharpFormEditorField::HIGHLIGHT,
                        SharpFormEditorField::SMALL,
                        SharpFormEditorField::CODE_BLOCK,
                        SharpFormEditorField::SUP,
                    ])
//                    ->hideToolbar()
//                    ->setWithoutParagraphs()
                    ->setCropRatio('1:1')
                    ->setHeight(350)
                    ->setStorageDisk('local')
                    ->setStorageBasePath('data'),
            )
            ->addField(
                SharpFormNumberField::make('number')
                    ->setLabel('Number')
                    ->setMin(0)
                    ->setMax(1)
                    ->setStep(.1),
            )
            ->addField(
                SharpFormSelectField::make('select_dropdown', $this->options(true))
                    ->setLocalized()
                    ->setLabel('Select dropdown')
                    ->setDisplayAsDropdown(),
            )
            ->addField(
                SharpFormSelectField::make('select_list', $this->options(true))
                    ->setLocalized()
                    ->setLabel('Select list')
                    ->setDisplayAsList(),
            )
            ->addField(
                SharpFormSelectField::make('select_list_multiple', $this->options(true))
                    ->setLocalized()
                    ->setLabel('Select list multiple')
                    ->setMultiple()
                    ->setDisplayAsList()
                    ->setMaxSelected(2),
            )
            ->addField(
                SharpFormTagsField::make('tags', $this->options(true))
                    ->setLocalized()
                    ->setLabel('Tags')
                    ->setCreatable(true)
                    ->setCreateAttribute('label')
                    ->setMaxTagCount(4),
            )
            ->addField(
                SharpFormTextareaField::make('textarea')
                    ->setLocalized()
                    ->setLabel('Textarea')
                    ->setMaxLength(50)
                    ->setRowCount(4),
            )
            ->addField(
                SharpFormUploadField::make('upload')
                    ->setLabel('Upload')
                    ->setFileFilterImages()
                    ->setCropRatio('1:1')
                    ->setStorageDisk('local')
                    ->setStorageBasePath('data'),
            )
            ->addField(
                SharpFormEditorField::make('wysiwyg')
                    ->setRenderContentAsMarkdown(false)
                    ->setLocalized()
                    ->setLabel('Wysiwyg')
                    ->setToolbar([
                        SharpFormEditorField::B, SharpFormEditorField::I, SharpFormEditorField::A,
                        SharpFormEditorField::SEPARATOR,
                        SharpFormEditorField::H1,
                        SharpFormEditorField::OL,
                        SharpFormEditorField::UL,
                        SharpFormEditorField::UPLOAD,
                        SharpFormEditorField::QUOTE,
                        SharpFormEditorField::CODE,
                        SharpFormEditorField::SEPARATOR,
                        SharpFormEditorField::CODE_BLOCK,
                    ])
//                    ->hideToolbar()
//                    ->setWithoutParagraphs()
                    ->setHeight(350, 0),
            );
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout
            ->addTab('Simple', function (FormLayoutTab $tab) {
                $tab
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withSingleField('text')
                            ->withSingleField('date')
                            ->withSingleField('check');
                    })
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withSingleField('number')
                            ->withSingleField('html');
                    });
            })
            ->addTab('Textarea', function (FormLayoutTab $tab) {
                $tab
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withSingleField('markdown')
                            ->withSingleField('textarea');
                    })
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withSingleField('wysiwyg');
                    });
            })
            ->addTab('Select', function (FormLayoutTab $tab) {
                $tab
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withSingleField('autocomplete_local')
                            ->withSingleField('autocomplete_remote')
                            ->withSingleField('select_dropdown');
                    })
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withSingleField('select_list')
                            ->withSingleField('select_list_multiple')
                            ->withSingleField('tags');
                    });
            })
            ->addTab('List', function (FormLayoutTab $tab) {
                $tab
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withSingleField('autocomplete_list', function (FormLayoutColumn $listItem) {
                            $listItem->withSingleField('item');
                        });
                    })
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withSingleField('list', function (FormLayoutColumn $listItem) {
                            $listItem->withFields('date|5', 'check|7')
                                ->withSingleField('markdown2');
                        });
                    });
            })
            ->addTab('Special', function (FormLayoutTab $tab) {
                $tab
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withSingleField('upload');
                    })
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withSingleField('geolocation');
                    });
            });
    }

    protected function findSingle()
    {
        if (! $rawData = (array) session()->get('sharp_test_form')) {
            $faker = \Faker\Factory::create();
            $rawData = [
                'text' => [
                    'fr' => $faker->words(3, true),
                    'en' => $faker->words(3, true),
                ],
                'autocomplete_local' => 1,
                'autocomplete_remote' => null,
                'autocomplete_list' => null,
                'check' => true,
                'date' => $faker->date('Y-m-d H:i'),
                'html' => [
                    'name' => $faker->name,
                ],
                'markdown' => [
                    'fr' => "Du **texte** avec *style* \n\n",
                    'en' => 'Some **text** with *style*',
                ],
                'number' => $faker->numberBetween(1, 100),
                'textarea' => [
                    'fr' => $faker->paragraph(3),
                    'en' => $faker->paragraph(3),
                ],
                'wysiwyg' => [
                    'fr' => '<p>fezfjklez fezjkflezjfkez fezjkflezjfklezjkflezj</p>',
                    'en' => 'some <strong>html stuff</strong>',
                ],
            ];
        }

        return $this->transform($rawData);
    }

    protected function updateSingle(array $data)
    {
        ray($data);

        session()->put('sharp_test_form', $data);
    }

    public function getDataLocalizations(): array
    {
        return ['fr', 'en'];
    }

    protected function options(bool $localized = false): array
    {
        if (! $localized) {
            return [
                '1' => 'Option one',
                '2' => 'Option two',
                '3' => 'Option three',
            ];
        }

        return [
            '1' => ['en' => 'Option one', 'fr' => 'Option un'],
            '2' => ['en' => 'Option two', 'fr' => 'Option deux'],
            '3' => ['en' => 'Option three', 'fr' => 'Option trois'],
        ];
    }
}
