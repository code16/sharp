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
use Illuminate\Contracts\Support\Arrayable;

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

    public function buildListConfig(): void
    {
        $this
            ->configureSearchable()
            ->configureDefaultSort('name', 'asc');
    }

    public function getListData(): array|Arrayable
    {
        $users = User::query()
            ->when($this->queryParams->hasSearch(), function ($query) {
                foreach ($this->queryParams->searchWords() as $word) {
                    $query->where('name', 'like', $word);
                }
            })
            ->orderBy(
                $this->queryParams->sortedBy() ?: 'name',
                $this->queryParams->sortedDir() ?: 'asc'
            );

        return $this->transform($users->paginate(30));
    }
}
</code-snippet>
@endverbatim

#### Forms
Forms are used to create or edit records.
@verbatim
<code-snippet name="Sharp Form" lang="php">
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
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
                    ->setMaxLength(150)
            )
            ->addField(
                SharpFormTextareaField::make('bio')
                    ->setLabel('Biography')
                    ->setMaxLength(500)
            );
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout
            ->addColumn(6, fn (FormLayoutColumn $column) => $column
                ->withField('name')
                ->withField('bio')
            );
    }

    public function find($id): array
    {
        return $this->transform(User::findOrFail($id));
    }

    public function update($id, array $data)
    {
        $this->validate($data, [
            'name' => ['required', 'string', 'max:150'],
        ]);

        $user = $id ? User::findOrFail($id) : new User();
        $user->fill($data);
        $user->save();

        return $user->id;
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
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class UserShow extends SharpShow
{
    protected function buildShowFields(FieldsContainer $showFields): void
    {
        $showFields
            ->addField(
                SharpShowTextField::make('name')
                    ->setLabel('Name')
            )
            ->addField(
                SharpShowTextField::make('email')
                    ->setLabel('Email')
            )
            ->addField(
                SharpShowTextField::make('bio')
                    ->setLabel('Biography')
            );
    }

    protected function buildShowLayout(ShowLayout $showLayout): void
    {
        $showLayout
            ->addSection(fn (ShowLayoutSection $section) => $section
                ->addColumn(6, fn (ShowLayoutColumn $column) => $column
                    ->withField('name')
                    ->withField('email')
                    ->withField('bio')
                )
            );
    }

    public function find(mixed $id): array
    {
        return $this->transform(User::findOrFail($id));
    }

    public function delete($id): void
    {
        User::findOrFail($id)->delete();
    }
}
</code-snippet>
@endverbatim

### Transformers
Sharp uses Transformers to map your model data to the format expected by the UI. Use `setCustomTransformer()` to transform field values before displaying them.
@verbatim
<code-snippet name="Sharp Transformer" lang="php">
// In Entity Lists, Forms, or Show Pages
public function getListData(): array|Arrayable
{
    $users = User::with('company')->get();

    return $this
        ->setCustomTransformer('name', function($value, $user) {
            return strtoupper($value);
        })
        ->setCustomTransformer('company:name', function($value, $user) {
            return $value ?? 'N/A';
        })
        ->transform($users);
}
</code-snippet>
@endverbatim

### Common Configuration Methods
@verbatim
<code-snippet name="Sharp Configuration Methods" lang="php">
// Entity List Configuration
public function buildListConfig(): void
{
    $this
        ->configureSearchable()
        ->configureDefaultSort('created_at', 'desc')
        ->configureDelete(confirmationText: 'Delete this item?')
        ->configureCreateButtonLabel('Add new...');
}

// Form Configuration
public function buildFormConfig(): void
{
    $this
        ->configureDisplayShowPageAfterCreation()
        ->configureCreateFormTitle('Create new user')
        ->configureEditFormTitle('Edit user');
}

// Show Configuration
public function buildShowConfig(): void
{
    $this
        ->configureBreadcrumbCustomLabelAttribute('name')
        ->configurePageTitle('name');
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
