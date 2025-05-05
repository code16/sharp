<?php

namespace App\Sharp;

use App\Models\Post;
use App\Models\User;
use App\Sharp\Entities\AuthorEntity;
use App\Sharp\Entities\PostEntity;
use Code16\Sharp\Search\SharpSearchEngine;
use Code16\Sharp\Utils\Links\LinkToEntityList;
use Code16\Sharp\Utils\Links\LinkToShowPage;

class AppSearchEngine extends SharpSearchEngine
{
    public function searchFor(array $terms): void
    {
        $this->searchForPosts($terms);
        $this->searchForAuthors($terms);
    }

    private function searchForPosts(array $terms): void
    {
        $resultSet = $this
            ->addResultSet(
                label: 'Posts',
                icon: 'lucide-file-text',
            )
            ->setEmptyStateLabel('No post found');

        if (! $resultSet->validateSearch(['min:2'], ['min' => 'Enter at least 2 characters'])) {
            return;
        }

        $builder = Post::query()
            ->with('author');

        foreach ($terms as $term) {
            $builder->where('title->en', 'like', $term);
        }

        $builder
            ->limit(10)
            ->get()
            ->each(function (Post $post) use ($resultSet) {
                $resultSet->addResultLink(
                    link: LinkToShowPage::make(PostEntity::class, $post->id),
                    label: $post->title,
                    detail: $post->author->name,
                );
            });
    }

    private function searchForAuthors(array $terms): void
    {
        $resultSet = $this
            ->addResultSet(
                label: 'Authors',
                icon: 'lucide-signature',
            );

        $builder = User::query();

        foreach ($terms as $term) {
            $builder->where(fn ($builder) => $builder
                ->orWhere('name', 'like', $term)
                ->orWhere('email', 'like', $term)
            );
        }

        $builder
            ->limit(10)
            ->get()
            ->each(function (User $author) use ($resultSet) {
                $resultSet->addResultLink(
                    link: LinkToEntityList::make(AuthorEntity::class)->setSearch($author->email),
                    label: $author->name,
                    detail: $author->email,
                );
            });
    }

    public function authorize(): bool
    {
        return true;
    }
}
