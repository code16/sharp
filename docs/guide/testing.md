# Testing

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
    ->use(SharpAssertions::class)
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
    ->assertListCount(3)
    ->assertListContains(['title' => 'My first post']);
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
    ->assertShowData([
        'title' => 'My first post',
        'author' => 'John Doe'
    ]);
```

### Instance Commands from Show

```php
$this->sharpShow(Post::class, 1)
    ->instanceCommand(PublishPost::class)
    ->post()
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
    ->assertFormData(['title' => 'Existing Post']);
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

## Testing Embedded Components

Show Pages can contain embedded Entity Lists or Dashboards. You can test them using `sharpListField()` and `sharpDashboardField()`.

### `sharpListField(string $entityKey)`

```php
$this->sharpShow(Post::class, 1)
    ->sharpListField(Comment::class)
    ->get()
    ->assertOk()
    ->assertListCount(5);
```

### `sharpDashboardField(string $entityKey)`

```php
$this->sharpShow(User::class, 1)
    ->sharpDashboardField(UserStatsDashboard::class)
    ->get()
    ->assertOk();
```
