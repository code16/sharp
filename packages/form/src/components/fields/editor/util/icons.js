

const toolbarIcons = {
    'bold': 'fas fa-bold',
    'italic': 'fas fa-italic',
    'strike': 'fas fa-strikethrough',
    'link': 'fas fa-link',
    'highlight': 'fas fa-highlighter',
    'h1': 'fas fa-heading',
    'h2': 'fas fa-heading fa-sm',
    'h3': 'fas fa-heading fa-xs',
    'quote': 'fas fa-quote-right',
    'code': 'fas fa-code',
    'ul': 'fas fa-list-ul',
    'ol': 'fas fa-list-ol',
    'indent': 'fas fa-indent',
    'de-indent': 'fas fa-outdent',
    'undo': 'fas fa-undo',
    'redo': 'fas fa-redo',
    'hr': 'fas fa-minus',
    'image': 'far fa-image',
    'document': 'fas fa-paperclip',
    'iframe': 'far fa-caret-square-right',
    'table': 'fas fa-table',
    'html': 'far fa-file-code',
};

export function getToolbarIcon(key) {
    return toolbarIcons[key] || null;
}
