import { getToolbarIcon } from "../../../../util/icons";
import SimpleMDE from 'simplemde';

export const buttons = {
    "bold": {
        name: "bold",
        action: SimpleMDE.toggleBold,
        className: getToolbarIcon('bold'),
        title: "Bold",
    },
    "italic": {
        name: "italic",
        action: SimpleMDE.toggleItalic,
        className: getToolbarIcon('italic'),
        title: "Italic",
    },
    "heading-1": {
        name: "heading-1",
        action: SimpleMDE.toggleHeading1,
        className: getToolbarIcon('h1'),
        title: "Big Heading"
    },
    "heading-2": {
        name: "heading-2",
        action: SimpleMDE.toggleHeading2,
        className: getToolbarIcon('h2'),
        title: "Medium Heading"
    },
    "heading-3": {
        name: "heading-3",
        action: SimpleMDE.toggleHeading3,
        className: getToolbarIcon('h3'),
        title: "Small Heading"
    },
    "code": {
        name: "code",
        action: SimpleMDE.toggleCodeBlock,
        className: getToolbarIcon('code'),
        title: "Code"
    },
    "quote": {
        name: "quote",
        action: SimpleMDE.toggleBlockquote,
        className: getToolbarIcon('quote'),
        title: "Quote",
    },
    "unordered-list": {
        name: "unordered-list",
        action: SimpleMDE.toggleUnorderedList,
        className: getToolbarIcon('ul'),
        title: "Generic List",
    },
    "ordered-list": {
        name: "ordered-list",
        action: SimpleMDE.toggleOrderedList,
        className: getToolbarIcon('ol'),
        title: "Numbered List",
    },
    "link": {
        name: "link",
        action: SimpleMDE.drawLink,
        className: getToolbarIcon('link'),
        title: "Create Link",
    },
    "image": {
        name: "image",
        action: SimpleMDE.drawImage,
        className: getToolbarIcon('image'),
        title: "Insert Image",
    },
    "horizontal-rule": {
        name: "horizontal-rule",
        action: SimpleMDE.drawHorizontalRule,
        className: getToolbarIcon('hr'),
        title: "Insert Horizontal Line"
    },
    "undo": {
        name: "undo",
        action: SimpleMDE.undo,
        className: getToolbarIcon('undo'),
        title: "Undo"
    },
    "redo": {
        name: "redo",
        action: SimpleMDE.redo,
        className: getToolbarIcon('redo'),
        title: "Redo"
    }
};