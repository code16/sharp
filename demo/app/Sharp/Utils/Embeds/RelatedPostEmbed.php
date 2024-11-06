<?php

namespace App\Sharp\Utils\Embeds;

use App\Enums\PostState;
use App\Models\Post;
use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class RelatedPostEmbed extends SharpFormEditorEmbed
{
    public function buildEmbedConfig(): void
    {
        $this
            ->configureLabel('Related Post')
            ->configureTagName('x-related-post')
            ->configureTemplate(<<<'blade'
                <div style="display: flex; gap: .5rem; align-items: center">
                    @if($online)
                        <span style="color: blue">●</span>
                    @else
                        <span style="color: orange">●</span>
                    @endif
                    <x-fas-link style="width: 1rem; height: 1rem" />
                    <em>{{ $title }}</em>
                </div>
                blade
            );
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $posts = Post::byAuthor(auth()->user())
            ->orderBy('title')
            ->pluck('title', 'id')
            ->toArray();

        $formFields
            ->addField(
                SharpFormSelectField::make('post', $posts)
                    ->setLabel('Post')
            );
    }

    public function transformDataForTemplate(array $data, bool $isForm): array
    {
        $post = Post::find($data['post']);

        return $this
            ->setCustomTransformer('title', function ($value) use ($post) {
                return $post?->title;
            })
            ->setCustomTransformer('online', function ($value) use ($post) {
                return $post?->state === PostState::ONLINE;
            })
            ->transformForTemplate($data);
    }

    public function updateContent(array $data = []): array
    {
        $this->validate($data, [
            'post' => ['required'],
        ]);

        return $data;
    }
}
