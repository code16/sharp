

const toolbarIcons = {
    'bold': 'fas fa-bold',
    'italic': 'fas fa-italic',
    'strike': 'fas fa-strikethrough',
    'link': 'fas fa-link',
    'highlight': 'fas fa-highlighter',
    'small': 'fas fa-font',
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
    'code-block': 'fas fa-file-code',
    'superscript': 'fas fa-superscript',
    'align-left': 'fas fa-align-left',
    'align-center': 'fas fa-align-center',
    'align-right': 'fas fa-align-right',
    'align-justify': 'fas fa-align-justify',
    'align-unset': 'fas fa-text-slash'

};

export function getToolbarIcon(key) {
    return toolbarIcons[key] || null;
}
