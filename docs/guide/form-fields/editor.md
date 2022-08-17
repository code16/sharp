# Editor

This form field is a rich text editor, with formatting and an optional toolbar.

Class: `Code16\Sharp\Form\Fields\SharpFormEditorField`

<img src="./editor.png" width="500">


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
const B = "bold";
const I = "italic";
const HIGHLIGHT = "highlight";
const UL = "unordered-list";
const OL = "ordered-list";
const SEPARATOR = "|";
const A = "link";
const H1 = "heading-1";
const H2 = "heading-2";
const H3 = "heading-3";
const UPLOAD_IMAGE = "upload-image";
const UPLOAD = "upload";
const TABLE = "table";
const IFRAME = "iframe";
const RAW_HTML = "html";
const UNDO = "undo";
const REDO = "redo";
const CODE = "code";
const QUOTE = "blockquote";
const HR = "horizontal-rule";
```

Example:

```php
SharpFormEditorField::make("description")
    ->setToolbar([
        SharpFormEditorField::B, SharpFormEditorField::I,
        SharpFormEditorField::SEPARATOR,
        SharpFormEditorField::UPLOAD_IMAGE,
        SharpFormEditorField::SEPARATOR,
        SharpFormEditorField::A,
     ]);
```

### `setRenderContentAsMarkdown(bool $renderAsMarkdown = true)`

If true te front will send the content as markdown to the back, for storage. Default is false.

### `setWithoutParagraphs(bool $withoutParagraphs = true)`

If true the editor won’t create `<p>`, but `<br>`. This is useful on some specific cases (everytime inline HTML is
needed, maybe for a title or a legend). Default is false.

## Embed images and files in content

The Editor field can directly embed images or regular files. This works with `UPLOAD_IMAGE` and `UPLOAD` tools from the
toolbar. To use this feature, add the tool in the toolbar and configure the environment:

### `setMaxFileSize(float $sizeInMB)`

Max file size allowed.

### `setTransformable(bool $transformable = true, bool $transformKeepOriginal = true)`

Allow the user to crop or rotate a visual, after the upload.  
With `$transformKeepOriginal` set to true, the original file will remain unchanged, meaning the transformations will be
stored directly in the `<x-sharp-image/>` tag. For instance:

```html
<x-sharp-image 
    name="filename.jpg"
    filter-crop="0.1495,0,0.5625,1"
    path="data/Spaceship/10/markdown/filename.jpg"
    disk="local">
</x-sharp-image>
```

Then at render Sharp will take care of that for the thumbnail (see *Display embedded files in the public site* below).

### `setCropRatio(string $ratio, array $croppableFileTypes = null)`

Set a ratio constraint to uploaded images, formatted like this: `width:height`. For instance: `16:9`, or `1:1`.

When a crop ratio is set, any uploaded picture will be auto-cropped (centered).

The second argument, `$croppableFileTypes`, provide a way to limit the crop configuration to a list of image files
extensions. For instance, it can be useful to define a crop for jpg and png, but not for gif because it would break
animation.

### `setStorageDisk(string $storageDisk)`

Set the destination storage disk (as configured in Laravel's  `config/filesystem.php` config file).

### `setStorageBasePath(string $storageBasePath)`

Set the destination base storage path. You can use the `{id}` special placeholder to add the instance id in the path.

For instance:
`$field->setStorageBasePath('/users/{id}/markdown')`

### `setFileFilter($fileFilter)`

Set the allowed file extensions. You can pass either an array, or a comma-separated list.

### `setFileFilterImages()`

Just a `setFileFilter([".jpg",".jpeg",".gif",".png"])` shorthand.

### Store images and files

Sharp takes care of copying the file at the right place (after image transformation, if wanted), based on the
configuration.

When inserting a file, the following tag is added in field text value:

```html
<x-sharp-file 
    name="filename.pdf"
    path="data/Spaceship/10/markdown/filename.pdf"
    disk="local">
</x-sharp-file>
```
In case of an image the inserted tag is:

```html
<x-sharp-image
    name="filename.jpg"
    path="data/Spaceship/10/markdown/filename.jpg"
    disk="local">
</x-sharp-image>
```

### Display embedded files / images in the public site

You may need to display those embedded files in the public website. The idea here is to display embedded images as
thumbnails, and other files as you need. Sharp provides a component for that:

```html
<x-sharp-content>
    {!! $html !!}
</x-sharp-content>
```

To handle image thumbnails, you can pass the following props:

```html
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
```html
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
php artisan vendor:publish --provider=Code16\\Sharp\\SharpServiceProvider --tag=views
```

Here are the parameters passed to the components:

- `$fileModel` which is a `SharpUploadModel` instance (see the [documentation](../sharp-uploads.md))
- `$width`, `$height`, `$filters`: whatever you passed as attribute

#### Handle markdown

The `<x-sharp-content>` component does not render markdown, you will have to use your own `<x-markdown>` component or helper function.
To make `<x-sharp-*>` elements working you must enable HTML in your parser 
(e.g. pass `['html_input' => 'allow']` to [league/commonmark](https://commonmark.thephpleague.com/2.0/configuration/))

Example:

- [Blade view file](https://github.com/code16/sharp/blob/e387562698a2908f0f575cc5fd96705b9b78e078/saturn/resources/views/pages/spaceships/spaceship.blade.php)
  with `<x-markdown>` usage
- [`Markdown` component](https://github.com/code16/sharp/blob/e387562698a2908f0f575cc5fd96705b9b78e078/saturn/app/View/Components/Markdown.php)
  using league/commonmark

::: warning
[cebe/markdown](https://github.com/cebe/markdown) is not compatible with sharp components
:::

## Custom embeds

This feature allows to embed any structured data in the content. A common use case is to embed a reference to another
instance, like for example: in a blog post, you want to insert a reference to another post, that would be rendered as
a "read also" block / link in the public section.

<img src="./editor-embeds.png">

In practice, the Editor field can allow custom embeds, which defines how the data is stored in the field (as HTML
attributes), and how it is edited in the UI, via a full-featured form.

### `allowEmbeds(array $embeds)`

This method expects an array of embeds that could be inserted in the content, declared as full class names. An embed
class must extend `Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed`.

The [documentation on how to write an Embed class is available here](../form-editor-embeds.md).

## Formatter

- `toFront`: expects a string; will extract embedded files for the front.
- `fromFront`: returns a string, handle files (format, transformation, copy).
