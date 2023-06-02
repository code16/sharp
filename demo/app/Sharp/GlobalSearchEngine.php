<?php

namespace App\Sharp;

use App\Models\Post;
use Code16\Sharp\Utils\Links\LinkToShowPage;

class GlobalSearchEngine
{
    public function searchFor(array $terms): void
    {
        $this->searchForPosts($terms);
    }

    private function searchForPosts(array $terms): void
    {
        $resultSet = $this->addResultSet(
            label: 'Posts',
            icon: 'fa-file-o',
        );
        
        $builder = Post::query();

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
                );
            });
    }
}