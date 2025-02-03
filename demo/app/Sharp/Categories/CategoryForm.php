<?php

namespace App\Sharp\Categories;

use App\Models\Category;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class CategoryForm extends SharpForm
{
    use WithSharpFormEloquentUpdater;

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('name')
                    ->setLabel('Name')
                    ->setMaxLength(150),
            )
            ->addField(
                SharpFormTextareaField::make('description')
                    ->setLabel('Description')
                    ->setRowCount(4)
                    ->setLocalized()
                    ->setMaxLength(500),
            );
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout
            ->addColumn(6, fn (FormLayoutColumn $column) => $column
                ->withField('name')
                ->withField('description')
            );
    }

    public function buildFormConfig(): void
    {
        $this->configureDisplayShowPageAfterCreation();
    }

    public function getDataLocalizations(): array
    {
        return ['fr', 'en'];
    }

    public function find($id): array
    {
        return $this->transform(Category::findOrFail($id));
    }

    public function update($id, array $data)
    {
        $this->validate(
            $data, [
                'name' => ['required', 'string', 'max:150'],
            ]
        );

        $category = $id
            ? Category::findOrFail($id)
            : Category::make(['order' => 100]);

        $this->save($category, $data);

        return $category->id;
    }
}
