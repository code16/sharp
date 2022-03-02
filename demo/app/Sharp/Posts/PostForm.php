<?php

namespace App\Sharp\Posts;

use App\Models\Post;
use App\Models\User;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
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
                    ->setStorageDisk('local')
                    ->setStorageBasePath('data/posts/{id}/embed')
                    ->setHeight(250)
            );
        
        if(currentSharpRequest()->isUpdate()) {
            $formFields->addField(
                SharpFormSelectField::make("author_id", User::orderBy('name')->pluck('name', 'id')->toArray())
                    ->setReadOnly(!auth()->user()->isAdmin())
                    ->setDisplayAsDropdown()
                    ->setLabel("Author")
                    ->setHelpMessage("This field is only editable by admins.")
            );
        }
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout
            ->addColumn(6, function (FormLayoutColumn $column) {
                $column->withSingleField("title");

                if(currentSharpRequest()->isUpdate()) {
                    $column->withSingleField("author_id");
                }
            })
            ->addColumn(6, function (FormLayoutColumn $column) {
                $column->withSingleField("content");
            });
    }

    public function buildFormConfig(): void
    {
        $this->configureDisplayShowPageAfterCreation();
    }

    public function find($id): array
    {
        return $this->transform(Post::findOrFail($id));
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
        
        if(currentSharpRequest()->isCreation()) {
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