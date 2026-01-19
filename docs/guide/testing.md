# Testing

::: tip INFO
This page documents the new Testing API. If you use the legacy one, please refer to [Testing (legacy)](testing-legacy.md).
:::

Sharp provides a fluent testing API to help you test your Sharp code. These assertions and helpers are designed to be used in Feature tests.

## The `SharpAssertions` trait

To use Sharp's testing helpers, include the `Code16\Sharp\Utils\Testing\SharpAssertions` trait in your TestCase class:

```php
use Code16\Sharp\Utils\Testing\SharpAssertions;

abstract class TestCase extends BaseTestCase
{
    use SharpAssertions;
    
    // ...
}
```

or in `Pest.php`:

```php
use Code16\Sharp\Utils\Testing\SharpAssertions;

pest()
    ->extend(\Tests\TestCase::class)
    ->use(SharpAssertions::class);
```

## Authentication

### `loginAsSharpUser($user)`

Sharp provides a helper to log in a user. By default, it will use the `SharpAssertions` internal logic to ensure the user is authorized to access Sharp.

```php
it('allows the user to access the list', function () {
    $user = User::factory()->create();

    $this
        ->loginAsSharpUser($user)
        ->sharpList(Post::class)
        ->get()
        ->assertOk();
});
```

## Testing Entity Lists

Use `sharpList()` to test your Entity Lists.

### `sharpList(string $entityKey)`

Starts a fluent interaction with an Entity List.

```php
$this->sharpList(Post::class)
    ->get()
    ->assertOk()
    ->assertListData(fn (AssertableJson $data) => $data
        ->count(3)
        ->has('0.title', 'My first post')
        ->etc()
    );
```

### Filtering the list

You can use `withFilter()` to apply filters to the list before calling `get()` or a command.

```php
$this->sharpList(Post::class)
    ->withFilter(CategoryFilter::class, 1)
    ->get()
    ->assertOk();
```

### Entity Commands

You can call an Entity Command directly from the list:

```php
$this->sharpList(Post::class)
    ->entityCommand(ExportPosts::class)
    ->post()
    ->assertOk()
    ->assertReturnsDownload('posts.csv');
```

If the command has a form, you can test it:
    
```php
$this->sharpList(Post::class)
    ->entityCommand(ExportPosts::class)
    ->getForm()
    ->assertFormData(fn (AssertableJson $data) => $data
        ->where('format', 'xls')
        ->etc()
    )
    ->post(['format' => 'csv'])
    ->assertOk();
```

### Instance Commands

Similarly, you can call an Instance Command:

```php
$this->sharpList(Post::class)
    ->instanceCommand(PublishPost::class, 1)
    ->post()
    ->assertOk()
    ->assertReturnsReload();
```

### Multi-step Commands (Wizards)

For commands that have multiple steps, you can use `getNextStepForm()`:

```php
$this->sharpList(Post::class)
    ->entityCommand(MyWizardCommand::class)
    ->getForm()
    ->post(['step1_data' => 'value'])
    ->assertReturnsStep('step2')
    ->getNextStepForm()
    ->assertFormData(fn (AssertableJson $data) => $data
        ->where('step2_field', 'default')
        ->etc()
    )
    ->post(['step2_data' => 'value'])
    ->assertOk();
```

## Testing Show Pages

Use `sharpShow()` to test your Show Pages.

### `sharpShow(string $entityKey, $instanceId)`

Starts a fluent interaction with a Show Page.

```php
$this->sharpShow(Post::class, 1)
    ->get()
    ->assertOk()
    ->assertShowData(fn (AssertableJson $data) => $data
        ->where('title', 'My first post')
        ->where('author', 'John Doe')
        ->etc()
    );
```

### Instance Commands from Show

```php
$this->sharpShow(Post::class, 1)
    ->instanceCommand(PublishPost::class)
    ->post()
    ->assertOk();
```

### List & dashboard fields

Show Pages can contain embedded Entity Lists or Dashboards. You can test them using `sharpListField()` and `sharpDashboardField()`.

#### `sharpListField(string $entityKey)`

```php
$this->sharpShow(Post::class, 1)
    ->sharpListField(Comment::class)
    ->get()
    ->assertOk()
    ->assertListData(fn (AssertableJson $data) => $data
        ->count(5)
    );
```

#### `sharpDashboardField(string $entityKey)`

```php
$this->sharpShow(User::class, 1)
    ->sharpDashboardField(UserStatsDashboard::class)
    ->get()
    ->assertOk();
```

### Nested shows

There are some cases where you have nested shows by navigating through Show List fields. You can chain `sharpShow()` calls to simulate the correct breadcrumb :

```php
$this->sharpList(Post::class)
    ->sharpShow(Post::class, 1)
    ->sharpListField(Comment::class)
    ->sharpShow(Comment::class, 1)
    ->assertOk();
```

## Testing Forms

Use `sharpForm()` to test your Forms.

### `sharpForm(string $entityKey, $instanceId = null)`

Starts a fluent interaction with a Form. If `$instanceId` is provided, it targets an edit form; otherwise, it targets a creation form.

### Creating and Updating

```php
// Create
$this->sharpForm(Post::class)
    ->store(['title' => 'New Post'])
    ->assertValid()
    ->assertRedirect();

// Update
$this->sharpForm(Post::class, 1)
    ->update(['title' => 'Updated Post'])
    ->assertValid()
    ->assertRedirect();
```

### Testing the "Creation" or "Edit" request itself

If you want to test that the form displays correctly:

```php
$this->sharpForm(Post::class, 1)
    ->edit()
    ->assertOk()
    ->assertFormData(fn (AssertableJson $data) => $data
        ->where('title', 'Existing Post')
        ->etc()
    );
```

From an `AssertableForm` (the result of `edit()` or `create()`), you can also call `update()` or `store()`:

```php
$this->sharpForm(Post::class, 1)
    ->edit()
    ->update(['title' => 'New title'])
    ->assertValid();
```

## Testing Dashboards

Use `sharpDashboard()` to test your Dashboards.

### `sharpDashboard(string $entityKey)`

Starts a fluent interaction with a Dashboard.

```php
$this->sharpDashboard(MyDashboard::class)
    ->get()
    ->assertOk();
```

### Filtering the dashboard

```php
$this->sharpDashboard(MyDashboard::class)
    ->withFilter(PeriodFilter::class, ['start' => '2023-01-01', 'end' => '2023-01-31'])
    ->get()
    ->assertOk();
```

### Dashboard Commands

```php
$this->sharpDashboard(MyDashboard::class)
    ->dashboardCommand(RefreshStats::class)
    ->post()
    ->assertOk();
```
