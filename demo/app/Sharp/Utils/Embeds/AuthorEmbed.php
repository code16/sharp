<?php

namespace App\Sharp\Utils\Embeds;

use App\Models\User;
use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteRemoteField;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\PageAlerts\PageAlert;
use Illuminate\Support\Arr;

class AuthorEmbed extends SharpFormEditorEmbed
{
    public function buildEmbedConfig(): void
    {
        $this
            ->configureLabel('Author')
            ->configureTagName('x-author')
            ->configureFormTemplatePath('sharp/templates/author_embed.vue');
    }

    protected function buildPageAlert(PageAlert $pageAlert): void
    {
        $pageAlert
            ->setLevelSecondary()
            ->setMessage('Please fill author detail below');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormAutocompleteRemoteField::make('author')
                    ->setLabel('Author')
                    ->setRemoteEndpoint('/api/admin/users')
                    ->setListItemTemplate('<div>{{name}}</div><div><small>{{email}}</small></div>')
//                    ->setListItemInlineTemplate('<div>{{name}}</div><div><small>{{email}}</small></div>')
//                    ->setResultItemInlineTemplate('<div>{{name}}</div><div><small>{{email}}</small></div>'),
            )
            ->addField(
                SharpFormUploadField::make('picture')
                    ->setMaxFileSize(1)
                    ->setImageOnly()
                    ->setLabel('Picture')
                    ->setImageCropRatio('1:1')
                    ->setStorageDisk('local')
                    ->setStorageBasePath('data/embeds'),
            )
            ->addField(
                SharpFormEditorField::make('slot')
                    ->setLabel('Biography'),
            );
    }

    public function transformDataForTemplate(array $data, bool $isForm): array
    {
        return $this
            ->setCustomTransformer('name', function ($value, $instance) {
                $user = ($instance['author'] ?? null) ? User::find($instance['author']) : null;

                return $user?->name;
            })
            ->setCustomTransformer('picture', (new SharpUploadModelFormAttributeTransformer())->dynamicInstance())
            ->transformForTemplate($data);
    }

    public function transformDataForFormFields(array $data): array
    {
        return $this
            ->setCustomTransformer('author', function ($value) {
                $user = $value ? User::find($value)?->toArray() : null;

                return $user
                    ? Arr::only($user, ['id', 'name', 'email'])
                    : null;
            })
            ->setCustomTransformer('picture', (new SharpUploadModelFormAttributeTransformer())->dynamicInstance())
            ->transform($data);
    }

    public function updateContent(array $data = []): array
    {
        $this->validate($data, [
            'author' => ['required'],
        ]);

        return $data;
    }
}
