<?php

namespace App\Sharp\TestForm;

use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteListField;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteLocalField;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteRemoteField;
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
    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('text')
                    ->setLocalized()
//                    ->setReadOnly()
                    ->setLabel('Text'),
            )
            ->addField(
                SharpFormAutocompleteLocalField::make('autocomplete_local')
                    ->setLabel('Autocomplete local')
                    ->setLocalSearchKeys(['label'])
                    ->setListItemTemplate('{{ $label }}')
                    ->setResultItemTemplate('{{ $label }} ({{ $id }})')
//                    ->setReadOnly()
                    ->setLocalValues($this->options()),
            )
            ->addField(
                SharpFormAutocompleteRemoteField::make('autocomplete_remote')
                    ->setLabel('Autocomplete remote')
                    ->setRemoteSearchAttribute('query')
                    ->setListItemTemplate('{{ $name }}')
                    ->setResultItemTemplate('{{ $name }} ({{ $id }})')
//                    ->setReadOnly()
                    ->setRemoteEndpoint(route('sharp.autocompletes.users.index')),
            )
            ->addField(
                SharpFormAutocompleteListField::make('autocomplete_list')
                    ->setLabel('Autocomplete_list')
                    ->setAddable()
                    ->setRemovable()
//                    ->setReadOnly()
                    ->setItemField(
                        SharpFormAutocompleteRemoteField::make('item')
                            ->setLabel('Passenger')
                            ->setPlaceholder('test')
                            ->setListItemTemplate('{{ $name }}')
                            ->setResultItemTemplate('{{ $name }} ({{ $id }})')
                            ->setRemoteEndpoint(route('sharp.autocompletes.users.index')),
                    ),
            )
            ->addField(
                SharpFormCheckField::make('check', 'Check')
//                    ->setReadOnly(),
            )
            ->addField(
                SharpFormDateField::make('datetime')
                    ->setLabel('Datetime')
                    ->setDisplayFormat('YYYY-MM-DD HH:mm')
                    ->setMinTime(0, 15)
//                    ->setReadOnly()
                    ->setHasTime(),
            )
            ->addField(
                SharpFormDateField::make('date')
                    ->setLabel('Date')
                    ->setDisplayFormat('YYYY-MM-DD')
//                    ->setReadOnly()
                    ->setHasTime(false),
            )
            ->addField(
                SharpFormDateField::make('time')
                    ->setLabel('Time')
                    ->setDisplayFormat('HH:mm')
                    ->setHasDate(false)
                    ->setMinTime(0, 15)
                    ->setMaxTime(22, 15)
//                    ->setReadOnly()
                    ->setHasTime(),
            )
            ->addField(
                SharpFormGeolocationField::make('geolocation')
                    ->setLabel('Geolocation')
                    ->setApiKey(env('GMAPS_KEY'))
                    ->setGoogleMapsMapId(env('GMAPS_MAP_ID'))
//                    ->setReadOnly()
                    ->setMapsProvider('osm')
                    ->setGeocodingProvider('osm')
//                    ->setMapsProvider('gmaps')
//                    ->setGeocodingProvider('gmaps')
    //                ->setDisplayUnitDecimalDegrees()
                    ->setDisplayUnitDegreesMinutesSeconds()
                    ->setGeocoding()
                    ->setInitialPosition(48.5838961, 7.742182599999978),
            )
            ->addField(
                SharpFormHtmlField::make('html')
//                    ->setReadOnly()
                    ->setLabel('Html')
                    ->setTemplate('Your name is <strong>{{ $name }}</strong>'),
            )
            ->addField(
                SharpFormListField::make('list')
                    ->setLabel('List')
                    ->setAddable()
                    ->setSortable()
//                    ->setReadOnly()
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
                SharpFormEditorField::make('wysiwyg')
                    ->setPlaceholder('Start typing content here...')
                    ->setMaxLength(200)
//                    ->setReadOnly()
                    ->setLocalized()
                    ->setLabel('Wysiwyg')
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
                    ->allowUploads(
                        SharpFormEditorUpload::make()
                            ->setImageOnly()
                            ->setImageCropRatio('1:1')
                            ->setStorageDisk('local')
                            ->setStorageBasePath('data')
                    )
                    ->setHeight(350)
            )
            ->addField(
                SharpFormEditorField::make('markdown')
                    ->setRenderContentAsMarkdown()
                    ->showCharacterCount()
//                    ->setReadOnly()
                    ->setLocalized()
                    ->setLabel('Markdown')
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
                    ->allowUploads(
                        SharpFormEditorUpload::make()
                            ->setImageOnly()
                            ->setImageCropRatio('1:1')
                            ->setStorageDisk('local')
                            ->setStorageBasePath('data')
                    )
//                    ->hideToolbar()
//                    ->setWithoutParagraphs()
                    ->setHeight(350, 0),
            )
            ->addField(
                SharpFormNumberField::make('number')
                    ->setLabel('Number')
//                    ->setReadOnly()
                    ->setMin(0)
                    ->setMax(1)
                    ->setStep(.1),
            )
            ->addField(
                SharpFormSelectField::make('select_dropdown', $this->options())
                    ->setLabel('Select dropdown')
//                    ->allowSelectAll()
//                    ->setClearable()
//                    ->setReadOnly()
                    ->setMultiple()
                    ->setDisplayAsDropdown(),
            )
            ->addField(
                SharpFormSelectField::make('select_list', $this->options())
                    ->setLabel('Select list')
//                    ->setReadOnly()
                    ->setDisplayAsList(),
            )
            ->addField(
                SharpFormSelectField::make('select_list_multiple', $this->options())
                    ->setLabel('Select list multiple')
//                    ->allowSelectAll()
//                    ->setInline()
                    ->setMultiple()
//                    ->setReadOnly()
                    ->setDisplayAsList()
//                    ->setMaxSelected(2),
            )
            ->addField(
                SharpFormTagsField::make('tags', $this->options())
                    ->setLabel('Tags')
//                    ->setReadOnly()
                    ->setCreatable(true)
                    ->setCreateAttribute('label')
                    ->setMaxTagCount(4),
            )
            ->addField(
                SharpFormTextareaField::make('textarea')
                    ->setLocalized()
//                    ->setReadOnly()
                    ->setLabel('Textarea')
                    ->setMaxLength(50)
                    ->setRowCount(4),
            )
            ->addField(
                SharpFormUploadField::make('upload')
                    ->setLabel('Upload')
                    ->setMaxFileSize(5)
//                    ->setReadOnly()
                    ->setImageCropRatio('1:1')
                    ->setStorageDisk('local')
                    ->setStorageBasePath('data'),
            )
            ->addField(CustomField::make('custom'));
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout
            ->addTab('Simple', function (FormLayoutTab $tab) {
                $tab
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withField('text')
                            ->withFields('datetime')
                            ->withFields('date|6', 'time|6')
                            ->withField('check');
                    })
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withField('number')
                            ->withField('html');
                    });
            })
            ->addTab('Textarea', function (FormLayoutTab $tab) {
                $tab
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withField('wysiwyg')
                            ->withField('textarea');
                    })
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withField('markdown');
                    });
            })
            ->addTab('Select', function (FormLayoutTab $tab) {
                $tab
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withField('autocomplete_local')
                            ->withField('autocomplete_remote')
                            ->withField('select_dropdown');
                    })
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withField('select_list')
                            ->withField('select_list_multiple')
                            ->withField('tags');
                    });
            })
            ->addTab('List', function (FormLayoutTab $tab) {
                $tab
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withListField('autocomplete_list', function (FormLayoutColumn $listItem) {
                            $listItem->withField('item');
                        });
                    })
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withListField('list', function (FormLayoutColumn $listItem) {
                            $listItem->withFields('date|5', 'check|7')
                                ->withField('markdown2');
                        });
                    });
            })
            ->addTab('Special', function (FormLayoutTab $tab) {
                $tab
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withField('upload');
                    })
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withField('geolocation');
                    });
            });
    }

    protected function findSingle()
    {
        if (! $rawData = (array) session()->get('sharp_test_form')) {
            $rawData = [
                'text' => [
                    'fr' => fake()->words(3, true),
                    'en' => fake()->words(3, true),
                ],
                'autocomplete_local' => 1,
                'autocomplete_remote' => null,
                'autocomplete_list' => null,
                'check' => true,
                'datetime' => fake()->date('Y-m-d H:i:s'),
                'date' => fake()->date('Y-m-d'),
                'time' => fake()->date('H:i:s'),
                'markdown' => [
                    'fr' => "Du **texte** avec *style* \n\n",
                    'en' => 'Some **text** with *style*',
                ],
                'number' => fake()->numberBetween(1, 100),
                'textarea' => [
                    'fr' => fake()->paragraph(3),
                    'en' => fake()->paragraph(3),
                ],
                'wysiwyg' => [
                    'fr' => '<p>fezfjklez fezjkflezjfkez fezjkflezjfklezjkflezj</p>',
                    'en' => 'some <strong>html stuff</strong>',
                ],
            ];
        }

        return $this
            ->setCustomTransformer('upload', (new SharpUploadModelFormAttributeTransformer())->dynamicInstance())
            ->setCustomTransformer('html', fn () => [
                'name' => fake()->name,
            ])
            ->transform($rawData);
    }

    public function rules(): array
    {
        return [
//            'date' => 'required|before_or_equal:'.date('Y-m-d'),
        ];
    }

    protected function updateSingle(array $data)
    {
        session()->put('sharp_test_form', $data);
    }

    public function getDataLocalizations(): array
    {
        return ['fr', 'en'];
    }

    protected function options(): array
    {
        return [
            '1' => 'Option one',
            '2' => 'Option two',
            '3' => 'Option three',
            '4' => 'Option four',
            '5' => 'Option five',
            '6' => 'Option six',
            '7' => 'Option seven',
            '8' => 'Option eight',
            '9' => 'Option nine',
            '10' => 'Option ten',
        ];
    }
}
