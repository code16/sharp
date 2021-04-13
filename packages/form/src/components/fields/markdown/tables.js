

function createListener(listener) {
    return (cm, event) => {
        listener(event);
    }
}

export function handleMarkdownTables(codemirror) {
    codemirror.on('dragover', createListener(onDragover));
    codemirror.on('drop', createListener(onDrop));
    codemirror.on('paste', createListener(onPaste));
}

function insertText(textarea, text) {
    const beginning = textarea.value.substring(0, textarea.selectionStart || 0)
    const remaining = textarea.value.substring(textarea.selectionEnd || 0)

    const newline = beginning.length === 0 || beginning.match(/\n$/) ? '' : '\n'
    const textBeforeCursor = beginning + newline + text

    textarea.value = textBeforeCursor + remaining
    textarea.selectionStart = textBeforeCursor.length
    textarea.selectionEnd = textarea.selectionStart

    textarea.dispatchEvent(
        new CustomEvent('change', {
            bubbles: true,
            cancelable: false
        })
    )

    textarea.focus()
}

function onDrop(event) {
    const transfer = event.dataTransfer;
    if (!transfer)
        return;
    if (hasFile(transfer))
        return;
    const table = hasTable(transfer);
    if (!table)
        return;
    event.stopPropagation();
    event.preventDefault();
    const field = event.currentTarget ?? event.target;
    if (field instanceof HTMLTextAreaElement) {
        insertText(field, tableMarkdown(table));
    }
}

function onDragover(event) {
    const transfer = event.dataTransfer;
    if (transfer)
        transfer.dropEffect = 'copy';
}

function onPaste(event) {
    if (!event.clipboardData)
        return;
    const table = hasTable(event.clipboardData);
    if (!table)
        return;
    event.stopPropagation();
    event.preventDefault();
    const field = event.currentTarget ?? event.target;
    if (field instanceof HTMLTextAreaElement) {
        insertText(field, tableMarkdown(table));
    }
}

function hasFile(transfer) {
    return Array.from(transfer.types).indexOf('Files') >= 0;
}

function columnText(column) {
    const noBreakSpace = '\u00A0';
    const text = (column.textContent || '').trim().replace(/\|/g, '\\|').replace(/\n/g, ' ');
    return text || noBreakSpace;
}

function findAlign(el) {
    const align = el.align || el.style.textAlign;
    if(align) {
        return align;
    }
    if(el.firstElementChild) {
        return findAlign(el.firstElementChild);
    }
}

function columnAlign(table, cell) {
    const index = Array.from(cell.parentElement.children).indexOf(cell);
    const columnCells = Array.from(table.querySelectorAll(`td:nth-child(${index + 1}), th:nth-child(${index + 1})`))
        .filter(cell => (cell.textContent || '').match(/\S/));
    if(!columnCells.length) {
        return null;
    }
    const headAlign = findAlign(columnCells[0]);
    if(columnCells.every(cell => findAlign(cell) === headAlign)) {
        return headAlign
    }
}

function tableHeaders(row) {
    return Array.from(row.querySelectorAll('td, th')).map(columnText);
}

function tableSpacers(table, row) {
    return Array.from(row.querySelectorAll('td, th')).map(cell => {
        const align = columnAlign(table, cell);
        if(align === 'right') {
            return ' --:';
        } else if(align === 'center') {
            return ':--:';
        }
        return ' -- ';
    });
}

function tableMarkdown(node) {
    const rows = Array.from(node.querySelectorAll('tr'));
    const firstRow = rows.shift();
    if (!firstRow)
        return '';
    const headers = tableHeaders(firstRow);
    const spacers = tableSpacers(node, firstRow);
    const header = `${headers.join(' | ')}\n${spacers.join('|')}\n`;
    const body = rows
        .map(row => {
            return Array.from(row.querySelectorAll('td')).map(columnText).join(' | ');
        })
        .join('\n');
    return `\n${header}${body}\n\n`;
}

function parseTable(html) {
    const el = document.createElement('div');
    el.innerHTML = html;
    return el.querySelector('table');
}

function hasTable(transfer) {
    if (Array.from(transfer.types).indexOf('text/html') === -1)
        return null;
    const html = transfer.getData('text/html');
    if (!/<table/i.test(html))
        return null;
    const table = parseTable(html);
    return !table || table.closest('[data-paste-markdown-skip]') ? null : table;
}
