<?php

namespace App\Sharp\Posts;

use App\Models\Category;
use App\Models\Post;
use App\Sharp\Utils\Embeds\AuthorEmbed;
use App\Sharp\Utils\Embeds\CodeEmbed;
use App\Sharp\Utils\Embeds\RelatedPostEmbed;
use App\Sharp\Utils\Embeds\TableOfContentsEmbed;
use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Code16\Sharp\Form\Fields\SharpFormDateField;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormTagsField;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\Layout\FormLayoutFieldset;
use Code16\Sharp\Form\Layout\FormLayoutTab;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class PostForm extends SharpForm
{
    use WithSharpFormEloquentUpdater;

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('title')
                    ->setLabel('Title')
                    ->setLocalized()
                    ->setMaxLength(150),
            )
            ->addField(
                SharpFormEditorField::make('content')
                    ->setLabel('Post content')
                    ->setLocalized()
                    ->setToolbar([
                        SharpFormEditorField::H2,
                        SharpFormEditorField::B,
                        SharpFormEditorField::UL,
                        SharpFormEditorField::A,
                        SharpFormEditorField::QUOTE,
                        SharpFormEditorField::SEPARATOR,
                        SharpFormEditorField::IFRAME,
                        SharpFormEditorField::UPLOAD,
                    ])
                    ->allowEmbeds([
                        RelatedPostEmbed::class,
                        AuthorEmbed::class,
                        CodeEmbed::class,
                        TableOfContentsEmbed::class,
                    ])
                    ->allowUploads(function (SharpFormEditorUpload $editorUpload) {
                        $editorUpload
                            ->setStorageDisk('local')
                            ->setStorageBasePath('data/posts/{id}/embed')
                            ->setMaxFileSize(1)
                            ->setHasLegend();
                    })
                    ->setMaxLength(1000)
                    ->setHeight(300, 0)
            )
            ->addField(
                SharpFormTagsField::make('categories', Category::pluck('name', 'id')->toArray())
                    ->setLabel('Categories')
                    ->setCreatable()
                    ->setCreateAttribute('name'),
            )
            ->addField(
                SharpFormUploadField::make('cover')
                    ->setMaxFileSize(1)
                    ->setImageOnly()
                    ->setLabel('Cover')
                    ->setCropRatio('16:9')
                    ->setStorageDisk('local')
                    ->setStorageBasePath('data/posts/{id}'),
            )
            ->addField(
                SharpFormTextField::make('meta_title')
                    ->setLabel('Title')
                    ->setMaxLength(100),
            )
            ->addField(
                SharpFormTextareaField::make('meta_description')
                    ->setLabel('Description')
                    ->setRowCount(4)
                    ->setMaxLength(250),
            )
            ->addField(
                SharpFormDateField::make('published_at')
                    ->setLabel('Publication date')
                    ->setHasTime()
                    ->setDisplayFormat('MM-DD-YYYY HH:mm'),
            )
            ->addField(
                SharpFormListField::make('attachments')
                    ->setLabel('Attachments')
                    ->setAddable()->setAddText('Add an attachment')
                    ->setRemovable()
                    ->setMaxItemCount(5)
                    ->setSortable()->setOrderAttribute('order')
                    ->allowBulkUploadForField('document')
                    ->addItemField(
                        SharpFormTextField::make('title')
                            ->setLabel('Title'),
                    )
                    ->addItemField(
                        SharpFormCheckField::make('is_link', 'It’s a link'),
                    )
                    ->addItemField(
                        SharpFormTextField::make('link_url')
                            ->setPlaceholder('URL of the link')
                            ->addConditionalDisplay('is_link'),
                    )
                    ->addItemField(
                        SharpFormUploadField::make('document')
                            ->setMaxFileSize(1)
                            ->setFileFilter(['pdf', 'zip'])
                            ->setStorageDisk('local')
                            ->setStorageBasePath('data/posts/{id}')
                            ->addConditionalDisplay('!is_link'),
                    ),
            )
            ->when(currentSharpRequest()->isUpdate(), fn ($formFields) => $formFields->addField(
                SharpFormAutocompleteField::make('author_id', 'remote')
                    ->setReadOnly(! auth()->user()->isAdmin())
                    ->setLabel('Author')
                    ->setRemoteEndpoint('/api/admin/users')
                    ->setListItemInlineTemplate('<div>{{name}}</div><div><small>{{email}}</small></div>')
                    ->setResultItemInlineTemplate('<div>{{name}}</div><div><small>{{email}}</small></div>')
                    ->setHelpMessage('This field is only editable by admins.'),
            ));
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout
            ->addTab('Content', function (FormLayoutTab $tab) {
                $tab
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column
                            ->withField('title')
                            ->when(
                                currentSharpRequest()->isUpdate(),
                                fn ($column) => $column->withField('author_id')
                            )
                            ->withFields('published_at', 'categories')
                            ->withField('cover')
                            ->withListField('attachments', function (FormLayoutColumn $item) {
                                $item->withFields('title|8', 'is_link|4')
                                    ->withField('link_url')
                                    ->withField('document');
                            });
                    })
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withField('content');
                    });
            })
            ->addTab('Metadata', function (FormLayoutTab $tab) {
                $tab
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withFieldset('Meta fields', function (FormLayoutFieldset $fieldset) {
                            $fieldset->withField('meta_title')
                                ->withField('meta_description');
                        });
                    });
            });
    }

    public function buildFormConfig(): void
    {
        $this->configureDisplayShowPageAfterCreation();
    }

    public function find($id): array
    {
        return $this
            ->setCustomTransformer('author_id', fn ($value, Post $instance) => $instance->author)
            ->setCustomTransformer('cover', new SharpUploadModelFormAttributeTransformer())
            ->setCustomTransformer('attachments[document]', new SharpUploadModelFormAttributeTransformer())
            ->transform(Post::with('cover', 'attachments', 'categories')->findOrFail($id));
    }

    public function rules(): array
    {
        return [
            'title.fr' => ['required', 'string', 'max:150'],
            'title.en' => ['required', 'string', 'max:150'],
            //            'content.text.fr' => ['required', 'string', 'max:2000'],
            //            'content.text.en' => ['required', 'string', 'max:2000'],
            'published_at' => ['required', 'date'],
            //            'attachments.*.title' => ['required', 'string', 'max:50'],
            //            'attachments.*.link_url' => ['required_if:attachments.*.is_link,true,1', 'nullable', 'url', 'max:150'],
            //            'attachments.*.document' => ['required_if:attachments.*.is_link,false,0'],
        ];
    }

    public function update($id, array $data)
    {
        $post = $id
            ? Post::findOrFail($id)
            : new Post([
                'author_id' => auth()->id(),
            ]);

        $this
            ->ignore(auth()->user()->isAdmin() ? [] : ['author_id'])
            ->save($post, $data);

        if (currentSharpRequest()->isCreation()) {
            $this->notify('Your post was created, but not published yet.');
        }

        return $post->id;
    }

    public function getDataLocalizations(): array
    {
        return ['en', 'fr'];
    }
}
