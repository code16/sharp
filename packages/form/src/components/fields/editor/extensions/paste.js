import { Extension } from "@tiptap/core";
import { Plugin } from 'prosemirror-state';
import { DOMParser, Schema } from 'prosemirror-model';

export const Paste = Extension.create({
    name: 'paste',
    addOptions: () => ({
        schema: null,
    }),
    addProseMirrorPlugins() {
        const schema = getNormalizedSchema(
            this.options.schema,
            this.editor.schema
        );
        const parser = DOMParser.fromSchema(schema);
        return [
            new Plugin({
                props: {
                    clipboardParser: parser,
                },
            })
        ]
    }
});

// needed to keep same references of node/mark types
function getNormalizedSchema(target, source) {
    const schema = new Schema(target.spec);
    schema.nodes = Object.fromEntries(
        Object.entries(source.nodes).filter(([key]) => !!target.nodes[key])
    );
    schema.marks = Object.fromEntries(
        Object.entries(source.marks).filter(([key]) => !!target.marks[key])
    );
    return schema;
}
