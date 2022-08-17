<?php

namespace App\Sharp\Posts;

use App\Models\Post;
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
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Utils\Links\LinkToEntityList;
use Code16\Sharp\Utils\Transformers\Attributes\Eloquent\SharpUploadModelThumbnailUrlTransformer;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;

class PostList extends SharpEntityList
{
    protected function buildListFields(EntityListFieldsContainer $fieldsContainer): void
    {
        $fieldsContainer
            ->addField(
                EntityListField::make('cover'),
            )
            ->addField(
                EntityListField::make('title')
                    ->setLabel('Title'),
            )
            ->addField(
                EntityListField::make('author:name')
                    ->setLabel('Author')
                    ->setSortable(),
            )
            ->addField(
                EntityListField::make('published_at')
                    ->setLabel('Published at')
                    ->setSortable(),
            );
    }

    protected function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout
            ->addColumn('cover', 1)
            ->addColumn('title', 4)
            ->addColumn('author:name', 3)
            ->addColumn('published_at', 4);
    }

    protected function buildListLayoutForSmallScreens(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout
            ->addColumn('title', 6)
            ->addColumn('published_at', 6);
    }

    public function buildListConfig(): void
    {
        $this
            ->configurePaginated()
            ->configureEntityState('state', PostStateHandler::class)
            ->configureDefaultSort('published_at', 'desc')
            ->configureSearchable();

        if (! auth()->user()->isAdmin()) {
            $this->configurePageAlert(
                'As an editor, you can only edit your posts; you can see other posts except those which are still in draft.',
                static::$pageAlertLevelSecondary,
            );
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
        ];
    }

    public function getListData(): array|Arrayable
    {
        $posts = Post::select('posts.*')
            ->with('author', 'categories')

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
                    collect($categories)
                        ->each(function ($categoryId) use ($builder) {
                            $builder->whereHas('categories', function (Builder $builder) use ($categoryId) {
                                return $builder->where('categories.id', $categoryId);
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
                    '<div><strong>fr</strong> %s</div><div><strong>en</strong> %s</div><div>%s</div>',
                    $instance->getTranslation('title', 'fr'),
                    $instance->getTranslation('title', 'en'),
                    $instance->categories
                        ->pluck('name')
                        ->map(fn ($name) => '<span class="badge">'.$name.'</span>')
                        ->implode(' '),
                );
            })
            ->setCustomTransformer('author:name', function ($value, $instance) {
                return $instance->author_id
                    ? LinkToEntityList::make('posts')
                        ->addFilter(AuthorFilter::class, $instance->author_id)
                        ->renderAsText($instance->author->name)
                    : null;
            })
            ->setCustomTransformer('cover', (new SharpUploadModelThumbnailUrlTransformer(100))->renderAsImageTag())
            ->setCustomTransformer('published_at', DateTimeCustomTransformer::class)
            ->transform($posts->paginate(20));
    }
}
