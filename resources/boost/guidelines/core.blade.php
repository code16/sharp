## Sharp
- Sharp is used by this application. Follow existing conventions for how and where it's implemented.
- Sharp is a content management framework for Laravel that allows you to define user interfaces in PHP using structured configuration objects.
- Sharp allows you to build Entity Lists, Forms, Show Pages, and Dashboards.

### Patterns
Use `make()` static methods to initialize fields, columns, and other components.

#### Entity Lists
Entity Lists are used to display a list of records.
@verbatim
<code-snippet name="Sharp Entity List" lang="php">
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\SharpEntityList;

class UserList extends SharpEntityList
{
    protected function buildList(EntityListFieldsContainer $fields): void
    {
        $fields
            ->addField(
                EntityListField::make('name')
                    ->setLabel('Name')
                    ->setSortable()
            )
            ->addField(
                EntityListField::make('email')
                    ->setLabel('Email')
            );
    }
}
</code-snippet>
@endverbatim

#### Forms
Forms are used to create or edit records.
@verbatim
<code-snippet name="Sharp Form" lang="php">
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class UserForm extends SharpForm
{
    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('name')
                    ->setLabel('Name')
                    ->setRequired()
            )
            ->addField(
                SharpFormEditorField::make('bio')
                    ->setLabel('Biography')
            );
    }
}
</code-snippet>
@endverbatim

#### Show Pages
Show Pages are used to display details of a single record.
@verbatim
<code-snippet name="Sharp Show Page" lang="php">
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class UserShow extends SharpShow
{
    protected function buildShowFields(FieldsContainer $showFields): void
    {
        $showFields
            ->addField(SharpShowTextField::make('name')->setLabel('Name'))
            ->addField(SharpShowTextField::make('email')->setLabel('Email'));
    }

    protected function buildShowLayout(ShowLayout $showLayout): void
    {
        $showLayout
            ->addSection('General', function ($section) {
                $section->addColumn(6, function ($column) {
                    $column->withFields('name', 'email');
                });
            });
    }
}
</code-snippet>
@endverbatim

### Transformers
Sharp uses Transformers to map your model data to the format expected by the UI.
@verbatim
<code-snippet name="Sharp Transformer" lang="php">
public function find($id): array
{
    return $this
        ->setCustomTransformer('name', function($value, $user) {
            return strtoupper($value);
        })
        ->transform(User::findOrFail($id));
}
</code-snippet>
@endverbatim

### Common Classes & Namespaces
- **Entity Lists:** `Code16\Sharp\EntityList\SharpEntityList`
- **Forms:** `Code16\Sharp\Form\SharpForm`
- **Show Pages:** `Code16\Sharp\Show\SharpShow`
- **Dashboards:** `Code16\Sharp\Dashboard\SharpDashboard`
- **Form Fields:** `Code16\Sharp\Form\Fields\...`
- **Show Fields:** `Code16\Sharp\Show\Fields\...`
- **Entity List Fields:** `Code16\Sharp\EntityList\Fields\...`
- **Eloquent Updater:** `Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater`
