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
use Code16\Sharp\Form\Fields\Editor\TextInputReplacement\EditorTextInputReplacement;
use Code16\Sharp\Form\Fields\Editor\TextInputReplacement\EditorTextInputReplacementPreset;
use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteRemoteField;
use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Code16\Sharp\Form\Fields\SharpFormDateField;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;
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
use Illuminate\Support\Facades\Blade;

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
                            ->setMaxFileSize(2)
                            ->setHasLegend()
                    )
                    ->setTextInputReplacements([
                        EditorTextInputReplacementPreset::frenchTypography(locale: 'fr', guillemets: true),
                        new EditorTextInputReplacement('/:\+1:/', '👍'),
                    ])
                    ->allowFullscreen()
                    ->setMaxLength(2000)
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
                    ->setImageCropRatio('16:9')
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
                    ->setHasTime(),
            )
            ->addField(
                SharpFormHtmlField::make('publication_label')
                    ->setLiveRefresh(linkedFields: ['author_id', 'published_at', 'attachments'])
                    ->setTemplate(function (array $data) {
                        if (! isset($data['published_at'])) {
                            return '';
                        }

                        return Blade::render(<<<'blade'
                            This post will be published on {{ $published_at }}
                            @if($author)
                                by {{ $author->name }}.
                            @endif
                            <br>
                            Including {{ count($attachments) }} attachments
                            ({{ count($fileAttachments) }} files, {{ count($linkAttachments) }} links).
                        blade, [
                            'published_at' => \Carbon\Carbon::parse($data['published_at'])->isoFormat('LLLL'),
                            'author' => \App\Models\User::find($data['author_id']),
                            'attachments' => $data['attachments'] ?? [],
                            'linkAttachments' => collect($data['attachments'] ?? [])->where('is_link', true),
                            'fileAttachments' => collect($data['attachments'] ?? [])->where('is_link', false),
                        ]);
                    })
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
                            ->setMaxFileSize(2)
                            ->setAllowedExtensions(['pdf', 'zip', 'mp4', 'mp3'])
                            ->setStorageDisk('local')
                            ->setStorageBasePath('data/posts/{id}')
                            ->addConditionalDisplay('!is_link'),
                    )
            )
            ->when(sharp()->context()->isUpdate(), fn ($formFields) => $formFields->addField(
                SharpFormAutocompleteRemoteField::make('author_id')
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
                                sharp()->context()->isUpdate(),
                                fn ($column) => $column->withField('author_id')
                            )
                            ->withFields('published_at', 'categories')
                            ->withField('publication_label')
                            ->withListField('attachments', function (FormLayoutColumn $item) {
                                $item->withFields(title: 8, is_link: 4)
                                    ->withField('link_url')
                                    ->withField('document');
                            });
                    })
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column
                            ->withField('cover')
                            ->withField('content');
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
        $this->configureDisplayShowPageAfterCreation()
            ->configureEditTitle('Edit post')
            ->configureCreateTitle('New post');
    }

    public function find($id): array
    {
        return $this
            ->setCustomTransformer('author_id', fn ($value, Post $instance) => $instance->author)
            ->setCustomTransformer('cover', new SharpUploadModelFormAttributeTransformer())
            ->setCustomTransformer('attachments[document]', new SharpUploadModelFormAttributeTransformer(withPlayablePreview: true))
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
