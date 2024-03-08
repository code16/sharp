<?php

namespace Code16\Sharp\Enums;

/**
 * @internal
 */
enum FormEditorToolbarButton: string
{
    case Bold = 'bold';
    case Italic = 'italic';
    case Highlight = 'highlight';
    case Small = 'small';
    case BulletList = 'bullet-list';
    case OrderedList = 'ordered-list';
    case Link = 'link';
    case Heading1 = 'heading-1';
    case Heading2 = 'heading-2';
    case Heading3 = 'heading-3';
    case Code = 'code';
    case Blockquote = 'blockquote';
    case Upload = 'upload';
    case UploadImage = 'upload-image';
    case HorizontalRule = 'horizontal-rule';
    case Table = 'table';
    case Iframe = 'iframe';
    case Html = 'html';
    case CodeBlock = 'code-block';
    case Superscript = 'superscript';

    case Undo = 'undo';
    case Redo = 'redo';

    case Separator = '|';
}
