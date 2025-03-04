<?php

namespace App\Sharp\Posts;

use App\Models\Post;
use App\Sharp\Entities\PostBlockEntity;
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
use Code16\Sharp\Utils\PageAlerts\PageAlert;
use Code16\Sharp\Utils\Transformers\Attributes\Eloquent\SharpUploadModelThumbnailUrlTransformer;

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
                    ->collapseToWordCount(40)
                    ->setLocalized()
            )
            ->addField(SharpShowTextField::make('author')->setLabel('Author'))
            ->addField(SharpShowTextField::make('categories')->setLabel('Categories'))
            ->addField(SharpShowPictureField::make('cover'))
            ->addField(
                SharpShowListField::make('attachments')
                    ->setLabel('Attachments')
                    ->addItemField(
                        SharpShowTextField::make('title')
                            ->setLabel('Title')
                    )
                    ->addItemField(
                        SharpShowTextField::make('link_url')
                            ->setLabel('External link')
                    )
                    ->addItemField(
                        SharpShowFileField::make('document')
                            ->setLabel('File')
                    )
            )
            ->addField(
                SharpShowEntityListField::make(PostBlockEntity::class)
                    ->setLabel('Blocks')
                    ->hideFilterWithValue('post', fn ($instanceId) => $instanceId)
            );
    }

    protected function buildShowLayout(ShowLayout $showLayout): void
    {
        $showLayout
            ->addSection(function (ShowLayoutSection $section) {
                $section
                    ->addColumn(7, function (ShowLayoutColumn $column) {
                        $column
                            ->withFields(categories: 5, author: 7)
                            ->withListField('attachments', function (ShowLayoutColumn $item) {
                                $item->withFields(title: 6, link_url: 6, document: 6);
                            });
                    })
                    ->addColumn(5, function (ShowLayoutColumn $column) {
                        $column->withField('cover');
                    });
            })
            ->addSection('Content', function (ShowLayoutSection $section) {
                $section
                    ->setKey('content-section')
                    ->addColumn(8, function (ShowLayoutColumn $column) {
                        $column->withField('content');
                    });
            })
            ->addEntityListSection(PostBlockEntity::class);
    }

    public function buildShowConfig(): void
    {
        $this
            ->configureEntityState('state', PostStateHandler::class)
            ->configureBreadcrumbCustomLabelAttribute('breadcrumb')
            ->configurePageTitleAttribute('title', localized: true)
            ->configureDeleteConfirmationText('Are you sure you want to delete this post (this will permanently delete its data)?');
    }

    protected function buildPageAlert(PageAlert $pageAlert): void
    {
        $pageAlert
            ->setLevelInfo()
            ->setMessage(function (array $data) {
                return $data['publication']['is_planned']
                    ? sprintf(
                        'This post is planned for publication, on %s',
                        $data['publication']['published_at'],
                    )
                    : null;
            });
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
        $post = Post::with('attachments', 'attachments.document')->findOrFail($id);

        return $this
            ->setCustomTransformer('breadcrumb', fn ($value, $instance) => str()
                ->of($instance->getTranslation('title', 'en'))
                ->limit(30)
            )
            ->setCustomTransformer('publication', fn ($value, Post $instance) => [
                'is_planned' => $instance->isOnline() && $instance->published_at->isFuture(),
                'published_at' => $instance->published_at->isoFormat('LLL'),
            ])
            ->setCustomTransformer('author', fn ($value, $instance) => $instance->author_id
                ? LinkToEntityList::make('authors')
                    ->setSearch($instance->author->email)
                    ->renderAsText($instance->author->name)
                : null
            )
            ->setCustomTransformer('categories', fn ($value, Post $instance) => $instance
                ->categories
                ->map(fn ($category) => LinkToShowPage::make('categories', $category->id)
                    ->renderAsText($category->name))
                ->implode(', ')
            )
            ->setCustomTransformer('cover', new SharpUploadModelThumbnailUrlTransformer(500))
            ->setCustomTransformer(
                'attachments[document]',
                new SharpUploadModelFormAttributeTransformer(withThumbnails: true)
            )
            ->setCustomTransformer('attachments[link_url]', fn ($value, $instance) => $instance->is_link
                ? sprintf('<a href="%s" alt="">%s</a>', $value, str($value)->limit(30))
                : null
            )
            ->transform($post);
    }

    public function delete(mixed $id): void
    {
        Post::findOrFail($id)->delete();
    }

    public function getDataLocalizations(): array
    {
        return ['en', 'fr'];
    }
}
