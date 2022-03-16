<?php

namespace App\Sharp\Utils\Embeds;

use App\Models\User;
use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
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
            ->configureInlineFormTemplate('<div><strong>{{ name }}</strong></div><div><small v-html="biography"></small></div>');
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
//                    ->setLocalized()
                    ->setLabel('Biography'),
            );
    }
    
    public function transformData(array $data, bool $isForm): array
    {
        $data["name"] = $data["author"] 
            ? (User::find($data["author"])?->name ?: null) 
            : null;
        
        return $data;
    }
    
    public function transformDataForForm(array $data): array
    {
        return $this
            ->setCustomTransformer('author', function ($value) {
                $user = $value ? User::find($value)?->toArray() : null;
                return $user 
                    ? Arr::only($user, ["id", "name", "email"])
                    : null;
            })
            ->setCustomTransformer('picture', function($value, $instance, $attribute) {
                if(!$value) {
                    return null;
                }
                
                if($value['name'] ?? null) {
                    return $value;
                } 
                
                $media = new SharpUploadModel($value);
                
                return (new SharpUploadModelFormAttributeTransformer())
                    ->apply($value, (object)[$attribute => $media], $attribute);
            })
            ->transform($data);
    }

    public function updateContent(array $data = []): array
    {
        $this->validate($data, [
            'author' => ['required'],
        ]);
        
        return $data;
    }
    
//    public function getDataLocalizations(): array
//    {
//        return ["en", "fr"];
//    }
}