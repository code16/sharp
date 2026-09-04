---
name: sharp-crud-scaffolding
description: Scaffold a full Sharp resource (Entity List, Form, Show Page) and wire it into Sharp - generator commands, the Entity class, config registration, and adding it to the menu. Use whenever adding a new Sharp-managed resource, not just when writing field code.
---

# Sharp CRUD Scaffolding

## When to use this skill
Use this when the user asks to add a new resource/model to the Sharp admin (e.g. "add a Sharp entity for Product", "make this model manageable in Sharp"). Writing an Entity List/Form/Show class alone is not enough - a Sharp resource only appears in the admin once its `Entity` class is registered and (usually) added to a menu.

## The pieces of one Sharp resource
1. **Entity List** (`Code16\Sharp\EntityList\SharpEntityList`) - the listing/table page.
2. **Form** (`Code16\Sharp\Form\SharpForm`) - create/edit page.
3. **Show** (`Code16\Sharp\Show\SharpShow`) - read-only detail page. Optional if edit-in-place via Form is enough.
4. **Entity** (`Code16\Sharp\Utils\Entities\SharpEntity`) - the glue class that declares which List/Form/Show/Policy belong together, and the label shown in the UI/breadcrumb.
5. **Policy** (optional) - `Code16\Sharp\Auth\SharpEntityPolicy` implementation, or an inline anonymous class returned from `getPolicy()`, to restrict view/update/delete.

## Prefer the generators
Sharp ships Artisan generators that produce these classes with the correct structure - use them instead of writing files by hand when scaffolding from scratch. There's also an interactive prompt-based wizard (`php artisan sharp:generator`), but it can't be driven non-interactively, so as an agent run the individual commands below instead:

```bash
php artisan sharp:make:entity-list ProductList --model="App\Models\Product"
php artisan sharp:make:form ProductForm --model="App\Models\Product"
php artisan sharp:make:show-page ProductShow --model="App\Models\Product"
php artisan sharp:make:policy ProductPolicy

# An Entity List is always included; add --form/--show for the pieces you generated above,
# and --policy if you generated a policy. There is no --list flag.
php artisan sharp:make:entity ProductEntity --label="Product" --form --show --policy
```

`--model` pre-fills the model class references in the generated List/Form/Show and offers to scaffold the Eloquent model if it doesn't exist yet. Generated classes land under `app/Sharp/` by convention (e.g. `app/Sharp/Entities/ProductEntity.php`), which is what `discoverEntities()` scans by default.

## The Entity class
Naming convention: singular, CamelCase, `Entity` suffix (e.g. `ProductEntity`).

```php
namespace App\Sharp\Entities;

use App\Sharp\ProductForm;
use App\Sharp\ProductList;
use App\Sharp\ProductShow;
use Code16\Sharp\Utils\Entities\SharpEntity;

class ProductEntity extends SharpEntity
{
    protected string $label = 'Product';
    protected ?string $list = ProductList::class;
    protected ?string $show = ProductShow::class;
    protected ?string $form = ProductForm::class;
    protected ?string $policy = ProductPolicy::class;
}
```

If you need to compute the label/classes dynamically (e.g. conditional policy), override the getter methods instead of the properties: `getLabel()`, `getList()`, `getShow()`, `getForm()`, `getPolicy()`.

For a dashboard-only entity, extend `SharpDashboardEntity` and set `$view` (not `$list`/`$form`/`$show`):

```php
use Code16\Sharp\Utils\Entities\SharpDashboardEntity;

class SalesDashboardEntity extends SharpDashboardEntity
{
    protected ?string $view = SalesDashboard::class;
}
```

## Registering the entity
This is the step most likely to be forgotten. In the app's `SharpAppServiceProvider::configureSharp()`:

```php
protected function configureSharp(SharpConfigBuilder $config): void
{
    $config
        ->setName('My project')
        // Preferred: auto-discovers every *Entity class under app/Sharp/Entities
        ->discoverEntities();

    // Or register explicitly (needed for entities living elsewhere, or to
    // control the key/icon/label used in menu links / entity maps):
    // ->addEntity('product', ProductEntity::class, 'lucide-package', 'Products');
}
```

If the app already calls `discoverEntities()` and the new Entity class is under the scanned path (default `app/Sharp/Entities`, or extra paths passed as an array argument), no further registration is needed - just confirm the class name ends in `Entity` and sits in a scanned directory.

## Adding it to the menu
Registering an entity makes it usable, but it won't show up in the sidebar until referenced in the app's `SharpMenu` implementation:

```php
class MySharpMenu extends Code16\Sharp\Utils\Menu\SharpMenu
{
    public function build(): self
    {
        return $this
            ->addEntityLink(ProductEntity::class, 'Products', icon: 'lucide-package');
    }
}
```

This menu class must itself be declared once in `configureSharp()` via `->setSharpMenu(MySharpMenu::class)` - it isn't auto-discovered like entities are.

## Checklist when scaffolding a new resource
- [ ] List/Form/Show classes created (generators preferred)
- [ ] Entity class created, pointing at List/Form/Show
- [ ] Entity registered (`discoverEntities()` covers it, or explicit `addEntity()`)
- [ ] Policy attached if the resource needs authorization beyond the default Gate
- [ ] Menu entry added if the resource should be reachable from the sidebar
