<?php

namespace App\Sharp\Categories;

use App\Models\Category;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class CategoryForm extends SharpForm
{
    use WithSharpFormEloquentUpdater;

    protected ?string $formValidatorClass = CategoryValidator::class;

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('name')
                    ->setLabel('Name')
                    ->setMaxLength(150),
            );
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout->addColumn(6, function (FormLayoutColumn $column) {
            $column->withSingleField('name');
        });
    }

    public function buildFormConfig(): void
    {
        $this->configureDisplayShowPageAfterCreation();
    }

    public function find($id): array
    {
        return $this->transform(Category::findOrFail($id));
    }

    public function update($id, array $data)
    {
        $category = $id
            ? Category::findOrFail($id)
            : new Category();

        $this->save($category, $data);

        return $category->id;
    }

    public function delete($id): void
    {
        Category::findOrFail($id)->delete();
    }
}
