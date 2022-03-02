<?php

namespace App\Sharp\Posts;

use App\Models\Post;
use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
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
    
    protected ?string $formValidatorClass = PostValidator::class;

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make("title")
                    ->setLabel("Title")
                    ->setLocalized()
                    ->setMaxLength(150)
            )
            ->addField(
                SharpFormEditorField::make("content")
                    ->setLabel("Post content")
                    ->setLocalized()
                    ->setToolbar([
                        SharpFormEditorField::H2,
                        SharpFormEditorField::B,
                        SharpFormEditorField::UL,
                        SharpFormEditorField::A,
                        SharpFormEditorField::QUOTE,
                        SharpFormEditorField::SEPARATOR,
                        SharpFormEditorField::UPLOAD,
                        SharpFormEditorField::IFRAME,
                    ])
                    ->setMaxFileSize(1)
                    ->setStorageDisk('local')
                    ->setStorageBasePath('data/posts/{id}/embed')
                    ->setHeight(250)
            )
            ->addField(
                SharpFormUploadField::make("cover")
                    ->setMaxFileSize(1)
                    ->setLabel("Cover")
                    ->setFileFilterImages()
                    ->setCropRatio("16:9")
                    ->setStorageDisk("local")
                    ->setStorageBasePath("data/posts/{id}")
            )
            ->addField(
                SharpFormTextField::make("meta_title")
                    ->setLabel("Title")
                    ->setMaxLength(100)
            )
            ->addField(
                SharpFormTextareaField::make("meta_description")
                    ->setLabel("Description")
                    ->setRowCount(4)
                    ->setMaxLength(250)
            );
        
        if(currentSharpRequest()->isUpdate()) {
            $formFields->addField(
                SharpFormAutocompleteField::make("author_id", "remote")
                    ->setReadOnly(!auth()->user()->isAdmin())
                    ->setLabel("Author")
                    ->setRemoteEndpoint("/api/admin/users")
                    ->setListItemInlineTemplate("<div>{{name}}</div><div><small>{{email}}</small></div>")
                    ->setResultItemInlineTemplate("<div>{{name}}</div><div><small>{{email}}</small></div>")
                    ->setHelpMessage("This field is only editable by admins.")
            );
        }
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout
            ->addTab("Content", function (FormLayoutTab $tab) {
                $tab
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withSingleField("title");
                        if (currentSharpRequest()->isUpdate()) {
                            $column->withSingleField("author_id");
                        }
                        $column->withSingleField("cover");
                    })
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withSingleField("content");
                    });
            })
            ->addTab("Metadata", function (FormLayoutTab $tab) {
                $tab
                    ->addColumn(6, function (FormLayoutColumn $column) {
                        $column->withFieldset("Meta fields", function (FormLayoutFieldset $fieldset) {
                            $fieldset->withSingleField("meta_title")
                                ->withSingleField("meta_description");
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
            ->setCustomTransformer("author_id", function ($value, Post $instance) {
                return $instance->author;
            })
            ->setCustomTransformer("cover", new SharpUploadModelFormAttributeTransformer())
            ->transform(Post::findOrFail($id));
    }

    public function update($id, array $data)
    {
        $post = $id 
            ? Post::findOrFail($id) 
            : new Post([
                'author_id' => auth()->id()
            ]);

        $this
            ->ignore(auth()->user()->isAdmin() ? [] : ['author_id'])
            ->save($post, $data);
        
        if(currentSharpRequest()->isCreation() && !$id) {
            $this->notify('Your post was created, but not published yet.');
        }

        return $post->id;
    }

    public function delete($id): void
    {
        Post::findOrFail($id)->delete();
    }

    public function getDataLocalizations(): array
    {
        return ["en", "fr"];
    }
}