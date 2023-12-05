import { Plugin } from '@tiptap/pm/state';


function insertFiles(files, { editor, pos }) {
    [...files]
        .reverse()
        .reduce((chain, file) =>
            chain.insertUpload({
                file,
                pos,
            }),
            editor.chain()
        )
        .run();
}

export function getEventsPlugin(editor) {
    return  new Plugin({
        props: {
            handlePaste(view, event) {
                const clipboardData = event.clipboardData || event.originalEvent.clipboardData;

                if(!clipboardData.files.length) {
                    return;
                }

                event.preventDefault();

                insertFiles(clipboardData.files, {
                    pos: editor.state.selection,
                    editor,
                });

                return false;
            },
            handleDOMEvents: {
                drop(view, event) {
                    if (!event.dataTransfer?.files?.length) {
                        return
                    }

                    event.preventDefault();

                    const coordinates = view.posAtCoords({ left: event.clientX, top: event.clientY });

                    insertFiles(event.dataTransfer.files, {
                        pos: coordinates.pos,
                        editor,
                    });

                    return true;
                },
            },
        },
    });
}
