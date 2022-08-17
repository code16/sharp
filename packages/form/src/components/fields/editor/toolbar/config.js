import { lang } from "sharp";
import { getToolbarIcon } from '../util/icons';


export const buttons = {
    'bold': {
        command: editor => editor.chain().focus().toggleBold().run(),
        isActive: editor => editor.isActive('bold'),
        icon: getToolbarIcon('bold'),
        label: lang('form.editor.toolbar.bold.title'),
    },
    'italic': {
        command: editor => editor.chain().focus().toggleItalic().run(),
        isActive: editor => editor.isActive('italic'),
        icon: getToolbarIcon('italic'),
        label: lang('form.editor.toolbar.italic.title'),
    },
    'highlight': {
        command: editor => editor.chain().focus().toggleHighlight().run(),
        isActive: editor => editor.isActive('highlight'),
        icon: getToolbarIcon('highlight'),
        label: lang('form.editor.toolbar.highlight.title'),
    },
    'small': {
        command: editor => editor.chain().focus().toggleSmall().run(),
        isActive: editor => editor.isActive('small'),
        icon: getToolbarIcon('small'),
        label: lang('form.editor.toolbar.small.title'),
    },
    'heading-1': {
        command: editor => editor.chain().focus().toggleHeading({ level: 1 }).run(),
        isActive: editor => editor.isActive('heading', { level: 1 }),
        icon: getToolbarIcon('h1'),
        label: lang('form.editor.toolbar.heading_1.title'),
    },
    'heading-2': {
        command: editor => editor.chain().focus().toggleHeading({ level: 2 }).run(),
        isActive: editor => editor.isActive('heading', { level: 2 }),
        icon: getToolbarIcon('h2'),
        label: lang('form.editor.toolbar.heading_2.title'),
    },
    'heading-3': {
        command: editor => editor.chain().focus().toggleHeading({ level: 3 }).run(),
        isActive: editor => editor.isActive('heading', { level: 3 }),
        icon: getToolbarIcon('h3'),
        label: lang('form.editor.toolbar.heading_3.title'),
    },
    'code': {
        command: editor => editor.chain().focus().toggleCode().run(),
        isActive: editor => editor.isActive('code'),
        icon: getToolbarIcon('code'),
        label: lang('form.editor.toolbar.code.title'),
    },
    'blockquote': {
        command: editor => editor.chain().focus().toggleBlockquote().run(),
        isActive: editor => editor.isActive('blockquote'),
        icon: getToolbarIcon('quote'),
        label: lang('form.editor.toolbar.quote.title'),
    },
    'bullet-list': {
        command: editor => editor.chain().focus().toggleBulletList().run(),
        isActive: editor => editor.isActive('bulletList'),
        icon: getToolbarIcon('ul'),
        label: lang('form.editor.toolbar.unordered_list.title'),
    },
    'ordered-list': {
        command: editor => editor.chain().focus().toggleOrderedList().run(),
        isActive: editor => editor.isActive('orderedList'),
        icon: getToolbarIcon('ol'),
        label: lang('form.editor.toolbar.ordered_list.title'),
    },
    'link': {
        command: (editor, { href, label }) => {
            const selection = editor.state.tr.selection;

            if(editor.isActive('link')) {
                editor.chain()
                    .focus()
                    .extendMarkRange('link')
                    .setLink({ href })
                    .run();

            } else if(selection.empty) {
                editor.chain()
                    .focus()
                    .insertContent(`<a href="${href}">${label || href}</a>`)
                    .run();

            } else {
                editor.chain().focus().setLink({ href }).run();
            }
        },
        isActive: editor => editor.isActive('link'),
        icon: getToolbarIcon('link'),
        label: lang('form.editor.toolbar.link.title'),
    },
    'upload-image': {
        command: editor => editor.chain().focus().newUpload().run(),
        isActive: editor => editor.isActive('upload') || editor.isActive('image'),
        icon: getToolbarIcon('image'),
        label: lang('form.editor.toolbar.upload_image.title'),
    },
    'upload': {
        command: editor => editor.chain().focus().newUpload().run(),
        isActive: editor => editor.isActive('upload'),
        icon: getToolbarIcon('document'),
        label: lang('form.editor.toolbar.upload.title'),
    },
    'horizontal-rule': {
        command: editor => editor.chain().focus().setHorizontalRule().run(),
        isActive: editor => editor.isActive('horizontalRule'),
        icon: getToolbarIcon('hr'),
        label: lang('form.editor.toolbar.horizontal_rule.title'),
    },
    'iframe': {
        command: editor => editor.chain().focus().insertIframe().run(),
        isActive: editor => editor.isActive('iframe'),
        icon: getToolbarIcon('iframe'),
        label: lang('form.editor.toolbar.iframe.title'),
    },
    'table': {
        command: editor => editor.chain().focus().insertTable().run(), // handled in TableDropdown
        isActive: editor => editor.isActive('table'),
        icon: getToolbarIcon('table'),
    },
    'html': {
        command: editor => editor.chain().focus().insertHtml().run(),
        isActive: editor => editor.isActive('html'),
        icon: getToolbarIcon('html'),
    },
    'code-block': {
        command: editor => editor.chain().focus().toggleCodeBlock().run(),
        isActive: editor => editor.isActive('codeBlock'),
        icon: getToolbarIcon('code-block'),
    },
    'superscript': {
        command: editor => editor.chain().focus().toggleSuperscript().run(),
        isActive: editor => editor.isActive('superscript'),
        icon: getToolbarIcon('superscript'),
    },
    'undo': {
        command: editor => editor.chain().undo().run(),
        icon: getToolbarIcon('undo'),
        label: lang('form.editor.toolbar.undo.title'),
    },
    'redo': {
        command: editor => editor.chain().redo().run(),
        icon: getToolbarIcon('redo'),
        label: lang('form.editor.toolbar.redo.title'),
    }
}
