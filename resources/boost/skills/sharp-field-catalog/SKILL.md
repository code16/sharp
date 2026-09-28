---
name: sharp-field-catalog
description: Field type catalog for Sharp Entity Lists, Forms, Show Pages, and Dashboard widgets, plus how validation actually works (rules(), not per-field setRequired()). Use when adding/choosing a field or widget class, or when validating form input.
---

# Sharp Field Catalog

## When to use this skill
Use this when deciding which field class fits a piece of data (e.g. "add a status dropdown", "let users upload a PDF", "show related orders on the show page") - to avoid guessing a class name or a fluent method that doesn't exist.

## Validation is not per-field
There is no `setRequired()` / `setRules()` on form fields. Validation happens in `SharpForm::rules()` (returned as standard Laravel validation rules, keyed by field name) or inline via `$this->validate($data, [...])` inside `update()`/`store()`. A field's `make()`/`setLabel()` chain only controls how it's *displayed and edited*, never whether it's required.

```php
public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:150'],
        'email' => ['required', 'email'],
    ];
}
```

## Form fields (`Code16\Sharp\Form\Fields\...`)
| Class | Use for | Notable config |
|---|---|---|
| `SharpFormTextField` | short text | `setMaxLength()` |
| `SharpFormTextareaField` | multi-line plain text | `setMaxLength()` |
| `SharpFormEditorField` | rich text (WYSIWYG/markdown) | `setToolbar()`, `setRenderContentAsMarkdown()`, `setMaxLength()` |
| `SharpFormHtmlField` | static read-only HTML block, not bound to data | - |
| `SharpFormNumberField` | numeric input | `setMin()`, `setMax()`, `setStep()` |
| `SharpFormCheckField` | single boolean checkbox | `setText()` |
| `SharpFormDateField` | date/datetime picker | `setHasTime()`, `setDisplayFormat()` |
| `SharpFormSelectField` | dropdown / radio / checkbox list from a **fixed** option list | options passed as 2nd arg to `make($key, $options)`; `setMultiple()`, `setDisplayAsList()`/`setDisplayAsDropdown()` |
| `SharpFormAutocompleteLocalField` | search-as-you-type over a **local, already-loaded** dataset | `setLocalValues()`, `setLocalSearchKeys()` |
| `SharpFormAutocompleteRemoteField` | search-as-you-type hitting a **remote endpoint/DB query** | `setRemoteEndpoint()` or `setRemoteCallback()`, `setRemoteSearchAttribute()` |
| `SharpFormTagsField` | multiple free-form or creatable tags | `setCreatable()`, `setMaxTagCount()` |
| `SharpFormListField` | repeatable group of sub-fields (an "array" of items) | `setAddable()`, `setSortable()`, add sub-fields via `addItemField()` |
| `SharpFormUploadField` | file/image upload | `setImageOnly()`, `setMaxFileSize()`, `setImageCropRatio()` |
| `SharpFormGeolocationField` | lat/lng picker on a map | `setGeocoding()`, `setApiKey()` |

Use `->addField(SharpFormSelectField::make('status', ['draft' => 'Draft', 'published' => 'Published']))` for a fixed-choice field - options are an associative array of `value => label` passed directly to `make()`, not set via a separate method.

## Show fields (`Code16\Sharp\Show\Fields\...`)
| Class | Use for |
|---|---|
| `SharpShowTextField` | plain/formatted text |
| `SharpShowPictureField` | image display |
| `SharpShowFileField` | downloadable file/document |
| `SharpShowListField` | repeated group of sub-values (mirrors `SharpFormListField`) |
| `SharpShowEntityListField` | embed another entity's Entity List inside this show page (e.g. an order's line items) |
| `SharpShowDashboardField` | embed a Dashboard view inside a show page |

## Entity List fields (`Code16\Sharp\EntityList\Fields\...`)
| Class | Use for |
|---|---|
| `EntityListField` | generic column (text, formatted value via a transformer) |
| `EntityListBadgeField` | colored badge/pill (e.g. status) |
| `EntityListStateField` | the entity's state selector column, when using [Entity States](https://sharp.code16.fr/docs/guide/entity-states) |

## Dashboard widgets (`Code16\Sharp\Dashboard\Widgets\...`)
| Class | Use for |
|---|---|
| `SharpFigureWidget` | a single KPI number, set via `setFigureData($key, $value)` in `buildWidgetsData()` |
| `SharpPanelWidget` | free-form HTML/content panel |
| `SharpOrderedListWidget` | a small ranked/ordered list (e.g. top products) |
| `SharpLineGraphWidget` / `SharpBarGraphWidget` / `SharpAreaGraphWidget` / `SharpPieGraphWidget` | time series / categorical charts, data supplied as one or more `SharpGraphWidgetDataSet` in `buildWidgetsData()` |

All widgets are declared in `buildWidgets()` and positioned in `buildDashboardLayout()`, but their actual values are only set in `buildWidgetsData()` - a widget with no matching `setFigureData()`/dataset call in that method will render empty.
