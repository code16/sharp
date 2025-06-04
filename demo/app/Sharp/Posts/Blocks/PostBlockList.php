<?php

namespace App\Sharp\Posts\Blocks;

use App\Models\Media;
use App\Models\PostBlock;
use Code16\Sharp\EntityList\Eloquent\SimpleEloquentReorderHandler;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Filters\HiddenFilter;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

class PostBlockList extends SharpEntityList
{
    protected function buildList(EntityListFieldsContainer $fields): void
    {
        $fields
            ->addField(
                EntityListField::make('type_label')
                    ->setWidth(.15)
                    ->setLabel('Type')
            )
            ->addField(
                EntityListField::make('content')
            );
    }

    public function buildListConfig(): void
    {
        $this->configureSubEntityAttribute('type')
            ->configureReorderable(new SimpleEloquentReorderHandler(PostBlock::class))
            ->configureQuickCreationForm();
    }

    protected function getFilters(): ?array
    {
        return [
            HiddenFilter::make('post'),
        ];
    }

    public function getListData(): array|Arrayable
    {
        $postBlocks = PostBlock::orderBy('order')
            ->where('post_id', $this->queryParams->filterFor('post'))
            ->get();

        return $this
            ->setCustomTransformer('type_label', fn ($value, PostBlock $instance) => sprintf(
                '<span class="badge badge-bloc badge-bloc-%1$s">%1$s</span>',
                $instance->type
            ))
            ->setCustomTransformer('content', fn ($value, PostBlock $instance) => match ($instance->type) {
                'text' => Str::limit($instance->content, 150),
                'video' => sprintf(
                    '<div style="display: flex; gap: .4rem; align-items: center">%s %s</div>',
                    svg('lucide-youtube')->toHtml(),
                    Str::match('/\ssrc="(.*)"/mU', $instance->content)
                ),
                'visuals' => $instance->files
                    ->map(function (Media $visual) {
                        if ($url = $visual->thumbnail(null, 30)) {
                            return sprintf('<img src="%s" alt="" style="display: inline-block">', $url);
                        }

                        return null;
                    })
                    ->implode(' ')
            })
            ->transform($postBlocks);
    }

    public function delete($id): void
    {
        PostBlock::findOrFail($id)->delete();
    }
}
