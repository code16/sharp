<?php

namespace Code16\Sharp\Form\Fields\Editor\Uploads;

use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\Layout\HasModalFormLayout;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\Fields\HandleFormFields;
use Code16\Sharp\Utils\Traits\HandleValidation;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;

/**
 * @internal
 */
class FormEditorUploadForm
{
    use HandleFormFields;
    use HandleValidation;
    use HasModalFormLayout;
    use WithCustomTransformers;

    public function __construct(
        protected SharpFormEditorUpload $editorUpload
    ) {
    }

    protected function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField($this->editorUpload)
            ->when($this->editorUpload->hasLegend(), function (FieldsContainer $formFields) {
                $formFields->addField(
                    SharpFormTextField::make('legend')
                        ->setLabel(__('sharp::form.editor.dialogs.upload.legend_field.label'))
                );
            });
    }

    protected function buildFormLayout(FormLayoutColumn $column): void
    {
        $column
            ->withField('file')
            ->when($this->editorUpload->hasLegend(), function (FormLayoutColumn $column) {
                $column->withField('legend');
            });
    }

    public function formLayout(): ?array
    {
        return $this->modalFormLayout(function (FormLayoutColumn $column) {
            $this->buildFormLayout($column);
        });
    }

    public function rules()
    {
        return [
            'file' => ['required', 'array'],
            'legend' => ['nullable'],
        ];
    }

    public function getDataLocalizations(): array
    {
        return [];
    }
}
