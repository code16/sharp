import { getToolbarIcon } from '../../../util/icons';


export const buttons = {
    'bold': {
        command: editor => editor.chain().focus().toggleBold().run(),
        icon: getToolbarIcon('bold'),
    },
    'italic': {
        command: editor => editor.chain().focus().toggleItalic().run(),
        icon: getToolbarIcon('italic'),
    },
    'heading-1': {
        command: editor => editor.chain().focus().toggleHeading({ level: 1 }).run(),
        icon: getToolbarIcon('h1'),
    },
    'heading-2': {
        command: editor => editor.chain().focus().toggleHeading({ level: 2 }).run(),
        icon: getToolbarIcon('h2'),
    },
    'heading-3': {
        command: editor => editor.chain().focus().toggleHeading({ level: 3 }).run(),
        icon: getToolbarIcon('h3'),
    },
    'code': {
        command: editor => editor.chain().focus().toggleCode().run(),
        icon: getToolbarIcon('code'),
    },
    'quote': {
        command: editor => editor.chain().focus().toggleBlockquote().run(),
        icon: getToolbarIcon('code'),
    },
    'unordered-list': {
        command: editor => editor.chain().focus().toggleBulletList().run(),
        icon: getToolbarIcon('ul'),
    },
    'ordered-list': {
        command: editor => editor.chain().focus().toggleOrderedList().run(),
        icon: getToolbarIcon('ol'),
    },
    'link': {
        command: editor => editor.chain().focus().toggleLink().run(),
        icon: getToolbarIcon('link'),
    },
    'image': {
        command: editor => {
            // todo
        },
        icon: getToolbarIcon('image'),
    },
    'document': {
        command: editor => {
            // todo
        },
        icon: getToolbarIcon('document'),
    },
    'horizontal-rule': {
        command: editor => editor.chain().focus().setHorizontalRule().run(),
        icon: getToolbarIcon('hr'),
    },
    'undo': {
        command: editor => editor.chain().undo().run(),
        icon: getToolbarIcon('undo'),
    },
    'redo': {
        command: editor => editor.chain().redo().run(),
        icon: getToolbarIcon('redo'),
    }
}
