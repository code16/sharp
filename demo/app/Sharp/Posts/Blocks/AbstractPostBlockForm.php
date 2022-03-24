<?php

namespace App\Sharp\Posts\Blocks;

use App\Models\PostBlock;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

abstract class AbstractPostBlockForm extends SharpForm
{
    use WithSharpFormEloquentUpdater;

    protected static string $postBlockType = 'none';
    protected static string $postBlockHelpText = '';

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormHtmlField::make('type')
                    ->setInlineTemplate('Post block type: <strong>{{name}}</strong><div class="small" v-if="help">{{help}}</div>'),
            );

        if ($field = $this->getContentField()) {
            $formFields->addField($field);
        }

        $this->buildAdditionalFields($formFields);
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout
            ->addColumn(6, function (FormLayoutColumn $column) {
                $column->withSingleField('type');
                if ($this->getContentField()) {
                    $column->withSingleField('content');
                }
                $this->addAdditionalFieldsToLayout($column);
            });
    }

    public function buildFormConfig(): void
    {
    }

    public function find($id): array
    {
        return $this
            ->setCustomTransformer('type', function () {
                return [
                    'name' => static::$postBlockType,
                    'help' => static::$postBlockHelpText,
                ];
            })
            ->addCustomTransformers()
            ->transform(PostBlock::with('files')->findOrFail($id));
    }

    protected function addCustomTransformers(): self
    {
        return $this;
    }

    public function create(): array
    {
        // Provide data to fill the dummy html field on creation
        return [
            'type' => [
                'name' => static::$postBlockType,
                'help' => static::$postBlockHelpText,
            ],
        ];
    }

    public function update($id, array $data)
    {
        $postBlock = $id
            ? PostBlock::findOrFail($id)
            : new PostBlock([
                'type' => static::$postBlockType,
                'post_id' => currentSharpRequest()->getPreviousShowFromBreadcrumbItems('posts')->instanceId(),
            ]);

        $this->save($postBlock, $data);

        return $postBlock->id;
    }

    public function delete($id): void
    {
        PostBlock::findOrFail($id)->delete();
    }

    protected function getContentField(): ?SharpFormField
    {
        return SharpFormTextareaField::make('content')
            ->setLabel('Content')
            ->setRowCount(6);
    }

    protected function buildAdditionalFields(FieldsContainer $formFields): void
    {
    }

    protected function addAdditionalFieldsToLayout(FormLayoutColumn $column): void
    {
    }
}
