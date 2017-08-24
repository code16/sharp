# Form field: Markdown

This form field is a markdown editor, with formatting and an optional toolbar.

Class: `Code16\Sharp\Form\Fields\SharpFormMarkdownField`


## `setHeight(int $height)`

Set the textarea height, in pixels.

## `showToolbar()` and `hideToolbar()`

Show or hide the toolbar (shown by default).

## `setToolbar(array $toolbar)`

Override the default toolbar, providing an array built with `SharpFormMarkdownField`'s constants:

    const B = "bold";
    const I = "italic";
    const UL = "unordered-list";
    const OL = "ordered-list";
    const SEPARATOR = "|";
    const A = "link";
    const H1 = "heading-1";
    const H2 = "heading-2";
    const H3 = "heading-3";
    const CODE = "code";
    const QUOTE = "quote";
    const IMG = "image";
    const HR = "horizontal-rule";

Example:

    SharpFormMarkdownField::make("description")
            ->setToolbar([
                SharpFormMarkdownField::B, SharpFormMarkdownField::I,
                SharpFormMarkdownField::SEPARATOR,
                SharpFormMarkdownField::IMG,
                SharpFormMarkdownField::SEPARATOR,
                SharpFormMarkdownField::A,
             ]);


## `setMaxImageSize(float $sizeInMB)`

The `IMG` tool (from the toolbar) allows the user to upload 