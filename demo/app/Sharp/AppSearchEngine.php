<?php

namespace App\Sharp;

use App\Models\Post;
use App\Models\User;
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
                icon: 'fa-file-o',
            )
            ->setEmptyStateLabel('No post found');
        
        if (! $resultSet->validateSearch(['min:3'], ['min' => 'Enter at least 3 characters'])) {
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
                    link: LinkToShowPage::make('posts', $post->id),
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
                icon: 'fa-users',
            )
            ->hideWhenEmpty();

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
                    link: LinkToEntityList::make('authors')->setSearch($author->email),
                    label: $author->name,
                    detail: $author->email,
                );
            });
    }
}
