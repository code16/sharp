<?php

namespace App\Sharp\Utils\Embeds;

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
            ->configureFormInlineTemplate('<div><span v-if="online" style="color: blue">●</span><i v-if="!online" style="color: orange">●</i> <i class="fa fa-link"></i> <em>{{ title }}</em></div>');
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
                return $post?->state === 'online';
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
