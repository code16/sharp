# Text

Class: `Code16\Sharp\Show\Fields\SharpShowTextField`

## Configuration

### `setLabel()`

Set the field label.

### `collapseToWordCount(int $wordCount)`

Collapse the text if too long, and add a "show more" link. Use it for long texts (even markdown formatted) in sections with only one field.

### `allowEmbeds(array $embeds)`

This method expects an array of embeds that could be inserted in the content, declared as full class names. An embed class must extend `Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed`.

The [documentation on how to write an Embed class is available here](../form-editor-embeds.md).

## Transformer

For markdown-formatted texts, be sure to use the built-in `MarkdownAttributeTransformer`:

```php
function find($id): array
{
    return $this
        ->setCustomTransformer(
            'description', 
            new MarkdownAttributeTransformer()
        )
        ->transform([...]);
}
```
