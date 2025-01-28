<?php

namespace App\Sharp\TestModels;

use App\Models\TestModel;
use App\Models\TestTag;
use App\Sharp\Embeds\TestEmbed;
use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
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
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class TestModelForm extends SharpForm
{
    use WithSharpFormEloquentUpdater;

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormAutocompleteLocalField::make('autocomplete_local')
                    ->setLabel('Autocomplete local')
                    ->setLocalSearchKeys(['label'])
                    ->setListItemTemplate('{{ $label }}')
                    ->setResultItemTemplate('{{ $label }} ({{ $id }})')
                    ->setLocalValues(static::options()),
            )
            ->addField(
                SharpFormAutocompleteRemoteField::make('autocomplete_remote')
                    ->setLabel('Autocomplete remote endpoint')
                    ->setRemoteSearchAttribute('query')
                    ->setListItemTemplate('{{ $label }}')
                    ->setResultItemTemplate('{{ $label }} ({{ $id }})')
                    ->setRemoteEndpoint(route('sharp.remote-autocomplete')),
            )
            ->addField(
                SharpFormAutocompleteRemoteField::make('autocomplete_remote2')
                    ->setLabel('Autocomplete remote callback')
                    ->setRemoteSearchAttribute('query')
                    ->setListItemTemplate('{{ $label }}')
                    ->setResultItemTemplate('{{ $label }} ({{ $id }})')
                    ->allowEmptySearch()
                    ->setRemoteCallback(function ($query) {
                        return collect(static::options())
                            ->filter(function ($label, $id) use ($query) {
                                return str_contains($label, $query);
                            })
                            ->map(function ($label, $id) {
                                return ['id' => $id, 'label' => $label];
                            })
                            ->values();
                    }),
            )
            ->addField(
                SharpFormAutocompleteListField::make('autocomplete_list')
                    ->setLabel('Autocomplete list')
                    ->setAddable()
                    ->setRemovable()
                    ->setItemField(
                        SharpFormAutocompleteLocalField::make('item')
                            ->setLabel('Autocomplete list item')
                            ->setPlaceholder('test')
                            ->setListItemTemplate('{{ $label }}')
                            ->setResultItemTemplate('{{ $label }} ({{ $id }})')
                            ->setLocalValues(static::options()),
                    ),
            )
            ->addField(
                SharpFormCheckField::make('check', 'Check')
            )
            ->addField(
                SharpFormDateField::make('date_time')
                    ->setLabel('Date time')
                    ->setMinTime(0, 15)
                    ->setHasTime(),
            )
            ->addField(
                SharpFormDateField::make('date')
                    ->setLabel('Date')
                    ->setHasTime(false),
            )
            ->addField(
                SharpFormDateField::make('time')
                    ->setLabel('Time')
                    ->setHasDate(false)
                    ->setMinTime(0, 15)
                    ->setMaxTime(22, 15)
                    ->setHasTime(),
            )
            ->addField(
                SharpFormGeolocationField::make('geolocation')
                    ->setLabel('Geolocation')
                    ->setApiKey(env('GMAPS_KEY'))
                    ->setGoogleMapsMapId(env('GMAPS_MAP_ID'))
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
                    ->setLabel('Html')
                    ->setTemplate('Your name is <strong>{{ $name }}</strong>'),
            )
            ->addField(
                SharpFormListField::make('list')
                    ->setLabel('List')
                    ->setAddable()
                    ->setSortable()
                    ->setRemovable()
                    ->setItemIdAttribute('id')
                    ->addItemField(
                        SharpFormTextField::make('item')
                            ->setLabel('List item text'),
                    )
            )
            ->addField(
                SharpFormEditorField::make('editor_html')
                    ->setPlaceholder('Start typing content here...')
                    ->setLabel('Editor HTML')
                    ->setToolbar([
                        SharpFormEditorField::B,
                        SharpFormEditorField::I,
                        SharpFormEditorField::A,
                        SharpFormEditorField::HIGHLIGHT,
                        SharpFormEditorField::SMALL,
                        SharpFormEditorField::SUP,
                        SharpFormEditorField::CODE,
                        SharpFormEditorField::SEPARATOR,
                        SharpFormEditorField::H1,
                        SharpFormEditorField::H2,
                        SharpFormEditorField::H3,
                        SharpFormEditorField::HR,
                        SharpFormEditorField::SEPARATOR,
                        SharpFormEditorField::OL,
                        SharpFormEditorField::UL,
                        SharpFormEditorField::QUOTE,
                        SharpFormEditorField::UPLOAD,
                        SharpFormEditorField::UPLOAD_IMAGE,
                        SharpFormEditorField::CODE_BLOCK,
                        SharpFormEditorField::SEPARATOR,
                        SharpFormEditorField::TABLE,
                        SharpFormEditorField::IFRAME,
                        SharpFormEditorField::RAW_HTML,
                    ])
                    ->allowUploads(
                        SharpFormEditorUpload::make()
//                            ->setImageOnly()
                            ->setImageCropRatio('1:1')
                            ->setStorageDisk('local')
                            ->setStorageBasePath('data')
                    )
                    ->allowEmbeds([
                        TestEmbed::class,
                    ])
                    ->setHeight(350)
            )
            ->addField(
                SharpFormEditorField::make('editor_html_localized')
                    ->setPlaceholder('Start typing content here...')
                    ->setLabel('Editor HTML localized')
                    ->setToolbar([
                        SharpFormEditorField::B,
                        SharpFormEditorField::I,
                        SharpFormEditorField::A,
                    ])
                    ->allowUploads(
                        SharpFormEditorUpload::make()
                            ->setImageOnly()
                            ->setImageCropRatio('1:1')
                            ->setStorageDisk('local')
                            ->setStorageBasePath('data')
                    )
                    ->setHeight(350)
                    ->setLocalized()
            )
            ->addField(
                SharpFormEditorField::make('editor_markdown')
                    ->setRenderContentAsMarkdown()
                    ->setLabel('Editor markdown')
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
                    ->setHeight(350, 0),
            )
            ->addField(
                SharpFormNumberField::make('number')
                    ->setLabel('Number')
                    ->setMin(0)
                    ->setMax(1)
                    ->setStep(.1),
            )
            ->addField(
                SharpFormSelectField::make('select_dropdown', static::options())
                    ->setLabel('Select dropdown')
                    ->setClearable()
                    ->setDisplayAsDropdown(),
            )
            ->addField(
                SharpFormSelectField::make('select_dropdown_multiple', static::options())
                    ->setLabel('Select dropdown multiple')
                    ->allowSelectAll()
                    ->setMultiple()
                    ->setDisplayAsDropdown(),
            )
            ->addField(
                SharpFormSelectField::make('select_radio', static::options())
                    ->setLabel('Select radios')
                    ->setDisplayAsList(),
            )
            ->addField(
                SharpFormSelectField::make('select_checkboxes', static::options())
                    ->setLabel('Select checkboxes')
                    ->allowSelectAll()
//                    ->setInline()
                    ->setMultiple()
                    ->setDisplayAsList()
                //                    ->setMaxSelected(2),
            )
            ->addField(
                SharpFormTagsField::make('tags', TestTag::pluck('label', 'id')->toArray())
                    ->setLabel('Tags')
                    ->setCreatable(true)
                    ->setCreateAttribute('label')
                    ->setMaxTagCount(4),
            )
            ->addField(
                SharpFormTextareaField::make('textarea')
                    ->setLabel('Textarea')
                    ->setRowCount(4),
            )
            ->addField(
                SharpFormTextareaField::make('textarea_localized')
                    ->setLocalized()
                    ->setLabel('Textarea localized')
                    ->setRowCount(4),
            )
            ->addField(
                SharpFormTextField::make('text')
                    ->setLabel('Text'),
            )
            ->addField(
                SharpFormTextField::make('text_localized')
                    ->setLabel('Text localized')
                    ->setLocalized(),
            )
            ->addField(
                SharpFormUploadField::make('upload')
                    ->setLabel('Upload')
                    ->setMaxFileSize(2)
                    ->setImageCropRatio('1:1')
                    ->setStorageDisk('local')
                    ->setStorageBasePath('data'),
            );
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout
            ->addColumn(6, function (FormLayoutColumn $column) {
                $column
                    ->withField('autocomplete_local')
                    ->withField('autocomplete_remote')
                    ->withField('autocomplete_remote2')
                    ->withListField('autocomplete_list', function (FormLayoutColumn $listItem) {
                        $listItem->withField('item');
                    })
                    ->withField('check')
                    ->withField('date')
                    ->withField('date_time')
                    ->withField('time')
                    ->withField('geolocation')
                    ->withField('html')
                    ->withListField('list', function (FormLayoutColumn $listItem) {
                        $listItem->withField('item');
                    })
                    ->withField('editor_html')
                    ->withField('editor_html_localized')
                    ->withField('editor_markdown');
            })
            ->addColumn(6, function (FormLayoutColumn $column) {
                $column
                    ->withField('number')
                    ->withField('select_dropdown')
                    ->withField('select_dropdown_multiple')
                    ->withField('select_radio')
                    ->withField('select_checkboxes')
                    ->withField('tags')
                    ->withField('textarea')
                    ->withField('textarea_localized')
                    ->withField('text')
                    ->withField('text_localized')
                    ->withField('upload');
            });
    }

    public function create(): array
    {
        return $this->transform(TestModel::make()->fill([
            'html' => ['name' => 'John'],
        ]));
    }

    public function find($id): array
    {
        return $this
            ->setCustomTransformer('autocomplete_remote', function ($value) {
                return $value ? ['id' => $value, 'label' => static::options()[$value]] : null;
            })
            ->setCustomTransformer('autocomplete_remote2', function ($value) {
                return $value ? ['id' => $value, 'label' => static::options()[$value]] : null;
            })
            ->setCustomTransformer('upload', new SharpUploadModelFormAttributeTransformer())
            ->transform(TestModel::with('tags')->findOrFail($id)->fill([
                'html' => ['name' => 'John'],
            ]));
    }

    public function update($id, array $data)
    {
        return $this->save(TestModel::findOrNew($id), $data)->id;
    }

    public function rules(): array
    {
        return [
            'editor_markdown' => [
                'nullable',
                'starts_with:**',
                'ends_with:**',
            ],
        ];
    }

    public function getDataLocalizations(): array
    {
        return ['en', 'fr'];
    }

    public static function options(): array
    {
        return [
            '1' => 'Option 1',
            '2' => 'Option 2',
            '3' => 'Option 3',
            '4' => 'Option 4',
            '5' => 'Option 5',
            '6' => 'Option 6',
            '7' => 'Option 7',
            '8' => 'Option 8',
            '9' => 'Option 9',
            '10' => 'Option 10',
        ];
    }
}
