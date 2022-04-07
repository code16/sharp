<?php

namespace App\Sharp\Posts\Blocks;

use App\Models\Media;
use App\Models\PostBlock;
use Code16\Sharp\EntityList\Commands\ReorderHandler;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

class PostBlockList extends SharpEntityList
{
    public function buildListConfig(): void
    {
        $this->configureMultiformAttribute('type')
            ->configureReorderable(new class implements ReorderHandler
            {
                public function reorder(array $ids): void
                {
                    PostBlock::whereIn('id', $ids)
                        ->get()
                        ->each(function (PostBlock $block) use ($ids) {
                            $block->update(['order' => array_search($block->id, $ids) + 1]);
                        });
                }
            });
    }

    public function getListData(): array|Arrayable
    {
        $postBlocks = PostBlock::orderBy('order')
            ->where('post_id', $this->queryParams->filterFor('post'))
            ->get();

        return $this
            ->setCustomTransformer('type_label', function ($value, PostBlock $instance) {
                return sprintf('<span class="badge badge-bloc badge-bloc-%1$s">%1$s</span>', $instance->type);
            })
            ->setCustomTransformer('content', function ($value, PostBlock $instance) {
                return match ($instance->type) {
                    'text' => Str::limit($instance->content, 150),
                    'video' => sprintf('<i class="fa fa-play-circle"></i> %s', Str::match('/\ssrc="(.*)"/mU', $instance->content)),
                    'visuals' => $instance->files
                        ->map(function (Media $visual) {
                            if ($url = $visual->thumbnail(null, 30)) {
                                return sprintf('<img src="%s" alt="" class="img-fluid">', $url);
                            }

                            return null;
                        },
                    )
                        ->implode(' ')
                };
            })
            ->transform($postBlocks);
    }

    protected function buildListFields(EntityListFieldsContainer $fieldsContainer): void
    {
        $fieldsContainer
            ->addField(EntityListField::make('type_label')->setLabel('Type'))
            ->addField(EntityListField::make('content'));
    }

    protected function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout
            ->addColumn('type_label', 2)
            ->addColumn('content');
    }
}
