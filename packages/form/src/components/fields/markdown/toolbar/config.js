import { getToolbarIcon } from '../../../../util/icons';


export const buttons = {
    'bold': {
        command: editor => editor.chain().focus().toggleBold().run(),
        isActive: editor => editor.isActive('bold'),
        icon: getToolbarIcon('bold'),
        bubbleMenu: true,
    },
    'italic': {
        command: editor => editor.chain().focus().toggleItalic().run(),
        isActive: editor => editor.isActive('italic'),
        icon: getToolbarIcon('italic'),
        bubbleMenu: true,
    },
    'heading-1': {
        command: editor => editor.chain().focus().toggleHeading({ level: 1 }).run(),
        isActive: editor => editor.isActive('heading', { level: 1 }),
        icon: getToolbarIcon('h1'),
    },
    'heading-2': {
        command: editor => editor.chain().focus().toggleHeading({ level: 2 }).run(),
        isActive: editor => editor.isActive('heading', { level: 2 }),
        icon: getToolbarIcon('h2'),
    },
    'heading-3': {
        command: editor => editor.chain().focus().toggleHeading({ level: 3 }).run(),
        isActive: editor => editor.isActive('heading', { level: 3 }),
        icon: getToolbarIcon('h3'),
    },
    'code': {
        command: editor => editor.chain().focus().toggleCode().run(),
        isActive: editor => editor.isActive('code'),
        icon: getToolbarIcon('code'),
    },
    'quote': {
        command: editor => editor.chain().focus().toggleBlockquote().run(),
        isActive: editor => editor.isActive('blockquote'),
        icon: getToolbarIcon('quote'),
    },
    'unordered-list': {
        command: editor => editor.chain().focus().toggleBulletList().run(),
        isActive: editor => editor.isActive('bulletList'),
        icon: getToolbarIcon('ul'),
    },
    'ordered-list': {
        command: editor => editor.chain().focus().toggleOrderedList().run(),
        isActive: editor => editor.isActive('orderedList'),
        icon: getToolbarIcon('ol'),
    },
    'link': {
        command: (editor, { href, label }) => {
            const selection = editor.state.tr.selection;
            if(selection.empty) {
                editor.chain().focus().insertContent(`<a href="${href}">${label || href}</a>`).run();
            } else {
                editor.chain().focus().toggleLink({ href }).run()
            }
        },
        isActive: editor => editor.isActive('link'),
        icon: getToolbarIcon('link'),
        bubbleMenu: true,
    },
    'image': {
        command: editor => editor.chain().focus().newUpload().run(),
        isActive: editor => editor.isActive('upload') || editor.isActive('image'),
        icon: getToolbarIcon('image'),
    },
    'document': {
        command: editor => editor.chain().focus().newUpload().run(),
        isActive: editor => editor.isActive('upload'),
        icon: getToolbarIcon('document'),
    },
    'horizontal-rule': {
        command: editor => editor.chain().focus().setHorizontalRule().run(),
        isActive: editor => editor.isActive('horizontalRule'),
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