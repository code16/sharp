<?php

namespace App\Sharp\Posts\Blocks;

use App\Models\PostBlock;
use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Show\Fields\SharpShowFileField;
use Code16\Sharp\Show\Fields\SharpShowListField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class PostBlockVisualsShow extends SharpShow
{
    protected function buildShowFields(FieldsContainer $showFields): void
    {
        $showFields
            ->addField(
                SharpShowListField::make('files')
                    ->setLabel('Visuals')
                    ->addItemField(
                        SharpShowFileField::make('file')
                    )
                    ->addItemField(
                        SharpShowTextField::make('legend')
                            ->setLabel('Legend')
                    )
            );
    }

    protected function buildShowLayout(ShowLayout $showLayout): void
    {
        $showLayout
            ->addSection(function (ShowLayoutSection $section) {
                $section
                    ->addColumn(12, function (ShowLayoutColumn $column) {
                        $column
                            ->withListField('files', function (ShowLayoutColumn $item) {
                                $item->withFields('file')
                                    ->withField('legend');
                            });
                    });
            });
    }

    public function find(mixed $id): array
    {
        $postBlock = PostBlock::findOrFail($id);

        return $this
            ->setCustomTransformer('files', new SharpUploadModelFormAttributeTransformer())
            ->transform($postBlock);
    }

    public function delete(mixed $id): void
    {
        PostBlock::findOrFail($id)->delete();
    }
}
