<?php

namespace App\Sharp\Posts;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Sharp\Utils\Embeds\AuthorEmbed;
use App\Sharp\Utils\Embeds\CodeEmbed;
use App\Sharp\Utils\Embeds\RelatedPostEmbed;
use App\Sharp\Utils\Embeds\TableOfContentsEmbed;
use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteRemoteField;
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
    
    protected SharpFormTextField $title {
        get => SharpFormTextField::make('title')
            ->setLabel('Title')
            ->setLocalized()
            ->setMaxLength(150);
    }
    
    protected SharpFormEditorField $content {
        get => SharpFormEditorField::make('content')
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
                AuthorEmbed::class,
                CodeEmbed::class,
            ])
            ->allowEmbeds([
                RelatedPostEmbed::class,
                AuthorEmbed::class,
                TableOfContentsEmbed::class,
                CodeEmbed::class,
            ])
            ->allowUploads(
                SharpFormEditorUpload::make()
                    ->setStorageDisk('local')
                    ->setStorageBasePath('data/posts/{id}/embed')
                    ->setMaxFileSize(1)
                    ->setHasLegend()
            )
            ->setMaxLength(2000)
            ->setHeight(300, 0);
    }
    
    protected SharpFormTagsField $categories {
        get => once(fn () =>
            SharpFormTagsField::make('categories', Category::pluck('name', 'id')->toArray())
                ->setLabel('Categories')
                ->setCreatable()
                ->setCreateAttribute('name')
        );
    }
    
    protected SharpFormUploadField $cover {
        get => SharpFormUploadField::make('cover')
            ->setMaxFileSize(1)
            ->setImageOnly()
            ->setLabel('Cover')
            ->setImageCropRatio('16:9')
            ->setStorageDisk('local')
            ->setStorageBasePath('data/posts/{id}');
    }
    
    protected SharpFormTextField $meta_title {
        get => SharpFormTextField::make('meta_title')
            ->setLabel('Title')
            ->setMaxLength(100);
    }
    
    protected SharpFormTextareaField $meta_description {
        get => SharpFormTextareaField::make('meta_description')
            ->setLabel('Description')
            ->setRowCount(4)
            ->setMaxLength(250);
    }
    
    protected SharpFormDateField $published_at {
        get => SharpFormDateField::make('published_at')
            ->setLabel('Publication date')
            ->setHasTime();
    }
    
    protected SharpFormListField $attachments {
        get => SharpFormListField::make('attachments')
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
                    ->setAllowedExtensions(['pdf', 'zip'])
                    ->setStorageDisk('local')
                    ->setStorageBasePath('data/posts/{id}')
                    ->addConditionalDisplay('!is_link')
            );
    }
    
    protected SharpFormAutocompleteRemoteField $author_id {
        get => SharpFormAutocompleteRemoteField::make('author_id')
            ->setReadOnly(! auth()->user()->isAdmin())
            ->setLabel('Author')
            ->allowEmptySearch()
            ->setRemoteCallback(function ($search) {
                $users = User::orderBy('name')->limit(10);

                foreach (explode(' ', trim($search)) as $word) {
                    $users->where(fn ($query) => $query
                        ->where('name', 'like', "%$word%")
                        ->orWhere('email', 'like', "%$word%")
                    );
                }

                return $users->get();
            })
            ->setListItemTemplate('<div>{{ $name }}</div><div><small>{{ $email }}</small></div>')
            ->setHelpMessage('This field is only editable by admins.');
    }
    
//    protected function getPropertyName(): ?string
//    {
//        $trace = array_find(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5), function ($trace) {
//            return str_ends_with($trace['function'], '::get');
//        });
//
//        return $trace ? str_replace(['$', '::get'], '', $trace['function']) : null;
//    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout
            ->addTab('Content', function (FormLayoutTab $tab) {
                $tab
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column
                            ->withField($this->title)
                            ->when(
                                sharp()->context()->isUpdate(),
                                fn ($column) => $column->withField($this->author_id)
                            )
                            ->withFields($this->published_at, $this->categories)
                            ->withListField($this->attachments, function (FormLayoutColumn $item) {
                                $item->withFields(title: 8, is_link: 4)
                                    ->withField('link_url')
                                    ->withField('document');
                            });
                    })
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column
                            ->withField($this->cover)
                            ->withField($this->content);
                    });
            })
            ->addTab('Metadata', function (FormLayoutTab $tab) {
                $tab
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withFieldset('Meta fields', function (FormLayoutFieldset $fieldset) {
                            $fieldset->withField($this->meta_title)
                                ->withField($this->meta_description);
                        });
                    });
            });
    }

    public function buildFormConfig(): void
    {
        $this->configureDisplayShowPageAfterCreation()
            ->configureEditTitle('Edit post')
            ->configureCreateTitle('New post');
    }

    public function find($id): array
    {
        return $this
            ->setCustomTransformer($this->author_id, fn ($value, Post $instance) => $instance->author)
            ->setCustomTransformer($this->cover, new SharpUploadModelFormAttributeTransformer())
            ->setCustomTransformer('attachments[document]', new SharpUploadModelFormAttributeTransformer())
            ->transform(Post::with('cover', 'attachments', 'categories')->findOrFail($id));
    }

    public function rules(): array
    {
        return [
            'title.fr' => ['required', 'string', 'max:150'],
            'title.en' => ['required', 'string', 'max:150'],
            'content.fr' => ['nullable', 'string', 'max:2000'],
            'content.en' => ['nullable', 'string', 'max:2000'],
            'published_at' => ['required', 'date'],
            'attachments.*.title' => ['required', 'string', 'max:150'],
            'attachments.*.link_url' => ['required_if:attachments.*.is_link,true,1', 'nullable', 'url', 'max:150'],
            'attachments.*.document' => ['required_if:attachments.*.is_link,false,0'],
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

        if (sharp()->context()->isCreation()) {
            $this->notify('Your post was created, but not published yet.');
        }

        return $post->id;
    }

    public function getDataLocalizations(): array
    {
        return ['en', 'fr'];
    }
}
