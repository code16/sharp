<?php

namespace App\Sharp\Posts;

use App\Models\Post;
use App\Sharp\Posts\Commands\BulkPublishPostsCommand;
use App\Sharp\Posts\Commands\ComposeEmailWithPostsWizardCommand;
use App\Sharp\Posts\Commands\EvaluateDraftPostWizardCommand;
use App\Sharp\Posts\Commands\PreviewPostCommand;
use App\Sharp\Utils\DateTimeCustomTransformer;
use App\Sharp\Utils\Filters\AuthorFilter;
use App\Sharp\Utils\Filters\CategoryFilter;
use App\Sharp\Utils\Filters\PeriodFilter;
use App\Sharp\Utils\Filters\StateFilter;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListStateField;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Utils\Links\LinkToEntityList;
use Code16\Sharp\Utils\PageAlerts\PageAlert;
use Code16\Sharp\Utils\Transformers\Attributes\Eloquent\SharpTagsTransformer;
use Code16\Sharp\Utils\Transformers\Attributes\Eloquent\SharpUploadModelThumbnailUrlTransformer;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;

class PostList extends SharpEntityList
{
    protected function buildList(EntityListFieldsContainer $fields): void
    {
        $fields
            ->addField(
                EntityListField::make('cover')
                    ->hideOnSmallScreens(),
            )
            ->addField(
                EntityListField::make('title')
                    ->setLabel('Title')
            )
            ->addField(
                EntityListStateField::make()
            )
            ->addField(
                EntityListField::make('author:name')
                    ->setLabel('Author')
                    ->hideOnSmallScreens()
                    ->setSortable(),
            )
            ->addField(
                EntityListField::make('categories')
                    ->setLabel('Categories')
                    ->hideOnSmallScreens()
            )
            ->addField(
                EntityListField::make('published_at')
                    ->setLabel('Published at')
                    ->setSortable(),
            );
    }

    public function buildListConfig(): void
    {
        $this
            ->configureCreateButtonLabel('New post...')
            ->configureEntityState('state', PostStateHandler::class)
            ->configureDefaultSort('published_at', 'desc')
            ->configureDelete(confirmationText: 'Are you sure you want to delete this post (this will permanently delete its data)?')
            ->configureSearchable();
    }

    protected function buildPageAlert(PageAlert $pageAlert): void
    {
        if (! auth()->user()->isAdmin()) {
            $pageAlert
                ->setMessage('As an editor, you can only edit your posts; you can see other posts except those which are still in draft.')
                ->setLevelSecondary();
        }
    }

    protected function getFilters(): ?array
    {
        return [
            StateFilter::class,
            AuthorFilter::class,
            CategoryFilter::class,
            PeriodFilter::class,
        ];
    }

    public function getInstanceCommands(): ?array
    {
        return [
            PreviewPostCommand::class,
            EvaluateDraftPostWizardCommand::class,
        ];
    }

    protected function getEntityCommands(): ?array
    {
        return [
            ComposeEmailWithPostsWizardCommand::class,
            BulkPublishPostsCommand::class,
        ];
    }

    public function getListData(): array|Arrayable
    {
        $posts = Post::select('posts.*')
            ->with('author', 'categories', 'cover')

            // Handle specific IDs (in case of refresh, called by a state handler or a command)
            ->when(
                $this->queryParams->specificIds(),
                function (Builder $builder, array $ids) {
                    $builder->whereIn('id', $ids);
                },
            )

            // Handle filters
            ->when(
                $this->queryParams->filterFor(StateFilter::class),
                function (Builder $builder, string $state) {
                    $builder->where('state', $state);
                },
            )
            ->when(
                $this->queryParams->filterFor(PeriodFilter::class),
                function (Builder $builder, array $dates) {
                    $builder->whereBetween('published_at', [$dates['start'], $dates['end']]);
                },
            )
            ->when(
                $this->queryParams->filterFor(AuthorFilter::class),
                function (Builder $builder, int $authorId) {
                    $builder->where('author_id', $authorId);
                },
            )
            ->when(
                $this->queryParams->filterFor(CategoryFilter::class),
                function (Builder $builder, $categories) {
                    $builder->where(function (Builder $builder) use ($categories) {
                        collect($categories)
                            ->each(function ($categoryId) use ($builder) {
                                $builder->orWhereHas(
                                    'categories',
                                    fn (Builder $builder) => $builder->where('categories.id', $categoryId)
                                );
                            });
                    });
                },
            )

            // Handle search words
            ->when(
                $this->queryParams->hasSearch(),
                function (Builder $builder) {
                    foreach ($this->queryParams->searchWords() as $word) {
                        $builder->where(function ($query) use ($word) {
                            $query
                                ->orWhere('title->fr', 'like', $word)
                                ->orWhere('title->en', 'like', $word);
                        });
                    }
                },
            )

            // Handle sorting
            ->when(
                $this->queryParams->sortedBy() === 'author:name',
                function (Builder $builder) {
                    $builder
                        ->leftJoin('users', 'posts.author_id', '=', 'users.id')
                        ->orderBy('users.name', $this->queryParams->sortedDir());
                },
                function (Builder $builder) {
                    $builder->orderBy('published_at', $this->queryParams->sortedDir() ?: 'desc');
                },
            );

        return $this
            ->setCustomTransformer('title', function ($value, Post $instance) {
                return sprintf(
                    '<div><strong>fr</strong> %s</div><div><strong>en</strong> %s</div>',
                    $instance->getTranslation('title', 'fr'),
                    $instance->getTranslation('title', 'en')
                );
            })
            ->setCustomTransformer('author:name', function ($value, $instance) {
                return $value
                    ? LinkToEntityList::make('posts')
                        ->addFilter(AuthorFilter::class, $instance->id)
                        ->setTooltip('See '.$value.' posts')
                        ->renderAsText($value)
                    : null;
            })
            ->setCustomTransformer('cover', (new SharpUploadModelThumbnailUrlTransformer(100))->renderAsImageTag())
            ->setCustomTransformer('published_at', DateTimeCustomTransformer::class)
            ->setCustomTransformer('categories', (new SharpTagsTransformer('name'))->setFilterLink('posts', CategoryFilter::class))
            ->transform($posts->paginate(20));
    }
}
