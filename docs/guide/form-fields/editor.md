# Editor

This form field is a rich text editor, with formatting and an optional toolbar.

Class: `Code16\Sharp\Form\Fields\SharpFormEditorField`

## Configuration

### `setHeight(int $height, int|null $maxHeight = null)`

Set the textarea height, in pixels.  
If `$maxHeight` is set, the field will auto-grow until:
- the indicated height in pixels
- on infinitely if set to `0`

### `showToolbar()`
### `hideToolbar()`

Show or hide the toolbar (shown by default).

### `setToolbar(array $toolbar)`

Override the default toolbar, providing an array built with `SharpFormEditorField`'s constants:

```php
const B = 'bold';
const I = 'italic';
const HIGHLIGHT = 'highlight';
const UL = 'unordered-list';
const OL = 'ordered-list';
const SEPARATOR = ''|'';
const A = 'link';
const H1 = 'heading-1';
const H2 = 'heading-2';
const H3 = 'heading-3';
const TABLE = 'table';
const IFRAME = 'iframe';
const RAW_HTML = 'html';
const UNDO = 'undo';
const REDO = 'redo';
const CODE = 'code';
const QUOTE = 'blockquote';
const HR = 'horizontal-rule';
```

Example:

```php
SharpFormEditorField::make("description")
    ->setToolbar([
        SharpFormEditorField::B, 
        SharpFormEditorField::I,
        SharpFormEditorField::SEPARATOR,
        SharpFormEditorField::A,
     ]);
```

If you have editor embeds you can add them to the toolbar alongside other buttons (instead of the embeds dropdown) :

```php
SharpFormEditorField::make("description")
    ->setToolbar([
        SharpFormEditorField::B, 
        SharpFormEditorField::I,
        AuthorEmbed::class,
    ])
    ->allowEmbeds([
        AuthorEmbed::class,
    ]);
```

See full [embed docs](../form-editor-embeds.md).

### `setRenderContentAsMarkdown(bool $renderAsMarkdown = true)`

If true the front will send the content as markdown to the back, for storage. Default is false.

### `setWithoutParagraphs(bool $withoutParagraphs = true)`

If true the editor won’t create `<p>`, but `<br>`. This is useful on some specific cases (everytime inline HTML is needed, maybe for a title or a legend). Default is false.

### `setMaxLength(int $maxLength)`

Set an informative max character count. Will enforce `showCharacterCount(true)`.

### `setMaxLengthUnlimited()`

Unset the max character count.

### `showCharacterCount(bool $showCharacterCount = true)`

Display a character count in the status bar. Default is false.

## Embed images and files in content

The Editor field can embed images or regular files. To use this feature, you must first allow the field to handle uploads:

### `allowUploads(SharpFormEditorEmbedUpload $formEditorUpload)`

This method allows the user to upload files and images in the editor:

```php
$formFields->addField(
    SharpFormEditorField::make('bio')
        ->allowUploads(
            SharpFormEditorEmbedUpload::make()
                ->setStorageBasePath('posts/embeds')
                ->setStorageDisk('local')
        )
);
```

The `SharpFormEditorEmbedUpload` can be configured with the same API as the `SharpFormUploadField`: `setMaxFileSize()`, `setImageOnly()`, `setAllowedExtensions()`, ... ([see full documentation](../form-fields/upload.md))

### A note on `setImageTransformable(bool $transformable = true, bool $transformKeepOriginal = true)`

As for a regular upload field, you can allow the user to crop or rotate the visual, after the upload.  
With `$transformKeepOriginal` set to true, the original file will remain unchanged, meaning the transformations will be stored directly in the `<x-sharp-image/>` tag. For instance:

```blade
{{-- (attribute JSON formatted for readability) --}}
<x-sharp-image 
    file='{
      "name":"image.jpg",
      "path": "data/Posts/1/image.jpg",
      "disk": "local",
      "filters": { "crop": { "x":0, "y":0, "width":.5, "height":.5 } } }
    '>
</x-sharp-image>
```

Then at render Sharp will take care of that for the thumbnail (see *Display embedded files in the public site* below).

### Store images and files

Sharp takes care of copying the file at the right place (after image transformation, if wanted), based on the configuration.

When inserting a file, the following tag is added in field text value:

```blade
{{-- (attribute JSON formatted for readability) --}}
<x-sharp-file 
    file='{
      "name": "doc.pdf",
      "path": "data/Posts/1/doc.pdf",
      "disk": "local"
    '>
</x-sharp-file>
```
In case of an image the inserted tag is:

```blade
{{-- (attribute JSON formatted for readability) --}}
<x-sharp-image
    file='{
      "name":"image.jpg",
      "path": "data/Posts/1/image.jpg",
      "disk": "local",
    '>
</x-sharp-image>
```

### Display embedded files / images in the public site

You may need to display those embedded files in the public website. The idea here is to display embedded images as thumbnails, and other files as you need. Sharp provides a component for that:

```blade
<x-sharp-content>
    {!! $html !!}
</x-sharp-content>
```

To handle image thumbnails, you can pass the following props:

```blade
<x-sharp-content
    :image-thumbnail-width="600"
    :image-thumbnail-height="400"
>
    {!! $html !!}
</x-sharp-content>
```

::: warning
`<x-sharp-content>` must have the editor content as direct child.
```diff
  <x-sharp-content>
-    <div>{!! $html !!}</div> {{-- this will not work --}}
+    {!! $html !!}
  </x-sharp-content>
```
:::

#### Advanced usages

To add custom attributes to `<x-sharp-image>` component you can use the following syntax:
```blade
<x-sharp-content>
    <x-sharp-content::attributes
        component="sharp-image"
        class="my-image h-auto"
        :width="600"
    />
    {!! $html !!}
</x-sharp-content>
```

#### Customize views

You can extend `<x-sharp-file>` and `<x-sharp-image>` components by publishing them:

```
php artisan vendor:publish --tag=sharp-views
```

Here are the parameters passed to the components:

- `$fileModel` which is a `SharpUploadModel` instance (see the [documentation](../sharp-uploads.md)); if you want to inject here your own `SharpUploadModel` implementation, you can do it by typing the full class path in the `sharp.uploads.model_class` config key.
- `$width`, `$height`, `$filters`: whatever you passed as attribute

#### Handle markdown

The `<x-sharp-content>` component does not render markdown, you will have to use your own `<x-markdown>` component or helper function.
To make `<x-sharp-*>` elements working you must enable HTML in your parser 
(e.g. pass `['html_input' => 'allow']` to [league/commonmark](https://commonmark.thephpleague.com/2.0/configuration/))

Example:

- [Blade view file](https://github.com/code16/sharp/blob/e387562698a2908f0f575cc5fd96705b9b78e078/saturn/resources/views/pages/spaceships/spaceship.blade.php) with `<x-markdown>` usage
- [`Markdown` component](https://github.com/code16/sharp/blob/e387562698a2908f0f575cc5fd96705b9b78e078/saturn/app/View/Components/Markdown.php) using league/commonmark

::: warning
[cebe/markdown](https://github.com/cebe/markdown) is not compatible with sharp components
:::

## Custom embeds

This feature allows to embed any structured data in the content. A common use case is to embed a reference to another instance, like for example: in a blog post, you want to insert a reference to another post, that would be rendered as a “read also” block / link in the public section.

In practice, the Editor field can allow custom embeds, which defines how the data is stored in the field (as HTML attributes), and how it is edited in the UI, via a full-featured form.

### `allowEmbeds(array $embeds)`

This method expects an array of embeds that could be inserted in the content, declared as full class names. An embed class must extend `Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed`.

The [documentation on how to write an Embed class is available here](../form-editor-embeds.md).

## Formatter

- `toFront`: expects a string; will extract embedded files for the front.
- `fromFront`: returns a string, handle files (format, transformation, copy).
