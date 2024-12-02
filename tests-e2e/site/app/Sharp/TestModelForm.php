<?php

namespace App\Sharp;

use App\Models\TestModel;
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
                    ->setLabel('autocomplete_local')
                    ->setLocalSearchKeys(['label'])
                    ->setListItemTemplate('{{ $label }}')
                    ->setResultItemTemplate('{{ $label }} ({{ $id }})')
                    ->setLocalValues($this->options()),
            )
            ->addField(
                SharpFormAutocompleteRemoteField::make('autocomplete_remote')
                    ->setLabel('autocomplete_remote')
                    ->setRemoteSearchAttribute('query')
                    ->setListItemTemplate('{{ $name }}')
                    ->setResultItemTemplate('{{ $name }} ({{ $id }})')
                    ->setRemoteEndpoint(route('sharp.autocompletes.users.index')),
            )
            ->addField(
                SharpFormAutocompleteListField::make('autocomplete_list')
                    ->setLabel('autocomplete_list')
                    ->setAddable()
                    ->setRemovable()
                    ->setItemField(
                        SharpFormAutocompleteLocalField::make('item')
                            ->setLabel('autocomplete_list_item')
                            ->setPlaceholder('test')
                            ->setListItemTemplate('{{ $name }}')
                            ->setResultItemTemplate('{{ $name }} ({{ $id }})')
                            ->setLocalValues($this->options()),
                    ),
            )
            ->addField(
                SharpFormCheckField::make('check', 'Check')
            )
            ->addField(
                SharpFormDateField::make('date_time')
                    ->setLabel('date_time')
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
                SharpFormTextField::make('text')
                    ->setLabel('text'),
            )
            ->addField(
                SharpFormTextField::make('text_localized')
                    ->setLocalized()
                    ->setLabel('text_localized'),
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
                        SharpFormDateField::make('date')
                            ->setLabel('Date')
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
                    ->setMin(0)
                    ->setMax(1)
                    ->setStep(.1),
            )
            ->addField(
                SharpFormSelectField::make('select_dropdown', $this->options())
                    ->setLabel('Select dropdown')
                    ->allowSelectAll()
//                    ->setClearable()
                    ->setMultiple()
                    ->setDisplayAsDropdown(),
            )
            ->addField(
                SharpFormSelectField::make('select_list', $this->options())
                    ->setLabel('Select list')
                    ->setDisplayAsList(),
            )
            ->addField(
                SharpFormSelectField::make('select_list_multiple', $this->options())
                    ->setLabel('Select list multiple')
                    ->allowSelectAll()
//                    ->setInline()
                    ->setMultiple()
                    ->setDisplayAsList()
//                    ->setMaxSelected(2),
            )
            ->addField(
                SharpFormTagsField::make('tags', $this->options())
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
                    ->setMaxFileSize(5)
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
                     ->withField('my_field');
             });
    }

    public function find($id): array
    {
        return $this->transform(TestModel::findOrNew($id));
    }

    public function update($id, array $data)
    {
        return $this->save(TestModel::findOrNew($id), $data)->id;
    }

    public function rules(): array
    {
    	return [
    	];
    }

    protected function options(): array
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
