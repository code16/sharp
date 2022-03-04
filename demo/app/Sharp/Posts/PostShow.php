<?php

namespace App\Sharp\Posts;

use App\Models\Post;
use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Show\Fields\SharpShowPictureField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\Links\LinkToEntityList;
use Code16\Sharp\Utils\Links\LinkToShowPage;
use Code16\Sharp\Utils\Transformers\Attributes\Eloquent\SharpUploadModelThumbnailUrlTransformer;
use Illuminate\Support\Str;

class PostShow extends SharpShow
{
    protected function buildShowFields(FieldsContainer $showFields): void
    {
        $showFields
            ->addField(SharpShowTextField::make('title_fr')->setLabel('Title (FR)'))
            ->addField(SharpShowTextField::make('title_en')->setLabel('Title (EN)'))
            ->addField(SharpShowTextField::make('content_fr')->collapseToWordCount(30))
            ->addField(SharpShowTextField::make('content_en')->collapseToWordCount(30))
            ->addField(SharpShowTextField::make('author')->setLabel('Author'))
            ->addField(SharpShowTextField::make('categories')->setLabel('Categories'))
            ->addField(SharpShowPictureField::make('cover'))
            ->addField(
                SharpShowEntityListField::make('blocks', 'blocks')
                    ->setLabel('Blocks')
                    ->hideFilterWithValue('post', function ($instanceId) {
                        return $instanceId;
                    }),
            );
    }

    protected function buildShowLayout(ShowLayout $showLayout): void
    {
        $showLayout
            ->addSection('', function (ShowLayoutSection $section) {
                $section
                    ->addColumn(6, function (ShowLayoutColumn $column) {
                        $column
                            ->withSingleField('title_fr')
                            ->withSingleField('title_en')
                            ->withSingleField('categories')
                            ->withSingleField('author');
                    })
                    ->addColumn(6, function (ShowLayoutColumn $column) {
                        $column->withSingleField('cover');
                    });
            })
            ->addSection('Content (FR)', function (ShowLayoutSection $section) {
                $section
                    ->addColumn(6, function (ShowLayoutColumn $column) {
                        $column->withSingleField('content_fr');
                    });
            })
            ->addSection('Content (EN)', function (ShowLayoutSection $section) {
                $section
                    ->addColumn(6, function (ShowLayoutColumn $column) {
                        $column->withSingleField('content_en');
                    });
            })
            ->addEntityListSection('blocks');
    }

    public function buildShowConfig(): void
    {
        $this
            ->configureEntityState('state', PostStateHandler::class)
            ->configureBreadcrumbCustomLabelAttribute('breadcrumb')
            ->configurePageAlert(
                '<span v-if="is_planed"><i class="fa fa-calendar"></i> This post is planed for publication, on {{published_at}}</span>',
                static::$pageAlertLevelInfo,
                'publication',
            );
    }

    public function find(mixed $id): array
    {
        return $this
            ->setCustomTransformer('breadcrumb', function ($value, $instance) {
                return Str::limit($instance->getTranslation('title', 'en'), 30);
            })
            ->setCustomTransformer('title_fr', function ($value, $instance) {
                return $instance->getTranslation('title', 'fr');
            })
            ->setCustomTransformer('title_en', function ($value, $instance) {
                return $instance->getTranslation('title', 'en');
            })
            ->setCustomTransformer('content_fr', function ($value, $instance) {
                return $instance->getTranslation('content', 'fr');
            })
            ->setCustomTransformer('content_en', function ($value, $instance) {
                return $instance->getTranslation('content', 'en');
            })
            ->setCustomTransformer('publication', function ($value, Post $instance) {
                return [
                    'is_planed' => $instance->isOnline() && $instance->published_at->isFuture(),
                    'published_at' => $instance->published_at->isoFormat('LLL'),
                ];
            })
            ->setCustomTransformer('author', function ($value, $instance) {
                return $instance->author_id
                    ? LinkToEntityList::make('authors')
                        ->setSearch($instance->author->email)
                        ->renderAsText($instance->author->name)
                    : null;
            })
            ->setCustomTransformer('categories', function ($value, Post $instance) {
                return $instance->categories
                    ->map(fn ($category) => LinkToShowPage::make('categories', $category->id)->renderAsText($category->name))
                    ->implode(', ');
            })
            ->setCustomTransformer('cover', new SharpUploadModelThumbnailUrlTransformer(300))
            ->transform(Post::findOrFail($id));
    }
}
