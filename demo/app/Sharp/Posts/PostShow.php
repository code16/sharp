<?php

namespace App\Sharp\Posts;

use App\Models\Post;
use App\Sharp\Posts\Commands\EvaluateDraftPostWizardCommand;
use App\Sharp\Posts\Commands\PreviewPostCommand;
use App\Sharp\Utils\Embeds\AuthorEmbed;
use App\Sharp\Utils\Embeds\CodeEmbed;
use App\Sharp\Utils\Embeds\RelatedPostEmbed;
use App\Sharp\Utils\Embeds\TableOfContentsEmbed;
use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Show\Fields\SharpShowFileField;
use Code16\Sharp\Show\Fields\SharpShowListField;
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
            ->addField(
                SharpShowTextField::make('content')
                    ->allowEmbeds([
                        RelatedPostEmbed::class,
                        AuthorEmbed::class,
                        CodeEmbed::class,
                        TableOfContentsEmbed::class,
                    ])
                    ->collapseToWordCount(30)
                    ->setLocalized()
            )
            ->addField(SharpShowTextField::make('author')->setLabel('Author'))
            ->addField(SharpShowTextField::make('categories')->setLabel('Categories'))
            ->addField(SharpShowPictureField::make('cover'))
            ->addField(
                SharpShowListField::make('attachments')
                    ->setLabel('Attachments')
                    ->addItemField(
                        SharpShowTextField::make('link_url')
                            ->setLabel('External link')
                    )
                    ->addItemField(
                        SharpShowFileField::make('document')
                            ->setLabel('File')
                            ->setStorageDisk('local')
                            ->setStorageBasePath('data/posts/{id}')
                    )
            )
            ->addField(
                SharpShowEntityListField::make('blocks', 'blocks')
                    ->setLabel('Blocks')
                    ->hideFilterWithValue('post', fn ($instanceId) => $instanceId)
            );
    }

    protected function buildShowLayout(ShowLayout $showLayout): void
    {
        $showLayout
            ->addSection('', function (ShowLayoutSection $section) {
                $section
                    ->addColumn(7, function (ShowLayoutColumn $column) {
                        $column
                            ->withSingleField('categories')
                            ->withSingleField('author')
                            ->withSingleField('attachments', function (ShowLayoutColumn $item) {
                                $item->withSingleField('link_url')
                                    ->withSingleField('document');
                            });
                    })
                    ->addColumn(5, function (ShowLayoutColumn $column) {
                        $column->withSingleField('cover');
                    });
            })
            ->addSection('Content', function (ShowLayoutSection $section) {
                $section
                    ->setKey('content-section')
                    ->addColumn(8, function (ShowLayoutColumn $column) {
                        $column->withSingleField('content');
                    });
            })
            ->addEntityListSection('blocks');
    }

    public function buildShowConfig(): void
    {
        $this
            ->configureEntityState('state', PostStateHandler::class)
            ->configureBreadcrumbCustomLabelAttribute('breadcrumb')
            ->configurePageTitleAttribute('title', localized: true)
            ->configurePageAlert(
                '<span v-if="is_planed"><i class="fa fa-calendar"></i> This post is planed for publication, on {{published_at}}</span>',
                static::$pageAlertLevelInfo,
                'publication',
            );
    }

    public function getInstanceCommands(): ?array
    {
        return [
            'content-section' => [
                PreviewPostCommand::class,
            ],
            EvaluateDraftPostWizardCommand::class,
            PreviewPostCommand::class,
        ];
    }

    public function find(mixed $id): array
    {
        return $this
            ->setCustomTransformer('breadcrumb', function ($value, $instance) {
                return Str::limit($instance->getTranslation('title', 'en'), 30);
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
            ->setCustomTransformer('cover', new SharpUploadModelThumbnailUrlTransformer(500))
            ->setCustomTransformer('attachments[document]', new SharpUploadModelFormAttributeTransformer(false))
            ->setCustomTransformer('attachments[link_url]', function ($value, $instance) {
                return $instance->is_link
                    ? sprintf('<a href="%s" alt="">%s</a>', $value, str($value)->limit(30))
                    : null;
            })
            ->transform(Post::with('attachments', 'attachments.document')->findOrFail($id));
    }

    public function getDataLocalizations(): array
    {
        return ['en', 'fr'];
    }
}
