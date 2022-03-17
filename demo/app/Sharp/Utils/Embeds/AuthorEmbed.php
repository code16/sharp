<?php

namespace App\Sharp\Utils\Embeds;

use App\Models\User;
use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Support\Arr;

class AuthorEmbed extends SharpFormEditorEmbed
{
    public function buildEmbedConfig(): void
    {
        $this
            ->configureLabel('Author')
            ->configureTagName('x-author')
            ->configurePageAlert('Please fill author detail below', static::$pageAlertLevelSecondary)
            ->configureInlineFormTemplate('<div><strong>{{ name }}</strong></div><div><small v-html="biography.text"></small></div>');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormAutocompleteField::make('author', 'remote')
                    ->setLabel('Author')
                    ->setRemoteEndpoint('/api/admin/users')
                    ->setListItemInlineTemplate('<div>{{name}}</div><div><small>{{email}}</small></div>')
                    ->setResultItemInlineTemplate('<div>{{name}}</div><div><small>{{email}}</small></div>'),
            )
            ->addField(
                SharpFormUploadField::make('picture')
                    ->setMaxFileSize(1)
                    ->setLabel('Picture')
                    ->setFileFilterImages()
                    ->setCropRatio('1:1')
                    ->setStorageDisk('local')
                    ->setStorageBasePath('data/embeds'),
            )
            ->addField(
                SharpFormEditorField::make('biography')
                    ->setLabel('Biography'),
            );
    }
    
    public function getDataForTemplate(array $data, bool $isForm): array
    {
        return $this
            ->setCustomTransformer('name', function($value, $instance) {
                return $instance["author"]
                    ? (User::find($instance["author"])?->name ?: null)
                    : null;
            })
            ->transform($data);
    }
    
    public function getDataForFormFields(array $data): array
    {
        return $this
            ->setCustomTransformer('author', function ($value) {
                $user = $value ? User::find($value)?->toArray() : null;
                return $user 
                    ? Arr::only($user, ["id", "name", "email"])
                    : null;
            })
            ->setCustomTransformer('picture', (new SharpUploadModelFormAttributeTransformer())->fakeInstance())
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