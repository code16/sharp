# Text

Class: `Code16\Sharp\Show\Fields\SharpShowTextField`

## Configuration

### `setLabel()`

Set the field label.

### `collapseToWordCount(int $wordCount)`

Collapse the text if too long, and add a "show more" link. Use it for long texts (even markdown formatted) in sections with only one field.

## Transformer

For markdown-formatted texts, be sure to use the built-in `MarkdownAttributeTransformer`:

```php
function find($id): array
{
    return $this
        ->setCustomTransformer(
            "description", 
            new MarkdownAttributeTransformer()
        )
        ->transform([...]);
}
```

See [related documentation of this transformer here](../how-to-transform-data.md#MarkdownAttributeTransformer).