import { AnyCommands, Node } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-3";
import UploadNode from "./UploadNode.vue";
import { getEventsPlugin } from "./events-plugin";
import { FormEditorFieldData, FormUploadFieldValueData } from "@/types";
import { serializeUploadAttributeValue } from "@/embeds/utils/attributes";
import { ExtensionAttributesSpec } from "@/form/components/fields/editor/types";

export type UploadNodeAttributes = {
    file: FormUploadFieldValueData,
    legend: string,
    isNew: boolean,
    nativeFile: File,
    isImage: boolean,
    notFound: boolean,
}

export type UploadOptions = {
    editorField: FormEditorFieldData,
}

export const Upload = Node.create<UploadOptions>({
    name: 'upload',

    group: 'block',

    atom: true,

    isolating: true,

    priority: 150,

    addAttributes(): ExtensionAttributesSpec<UploadNodeAttributes> {
        return {
            file: {
                default: null,
                parseHTML: element => ({
                    file: JSON.parse(element.getAttribute('file') ?? 'null'),
                }),
                renderHTML: (attributes) => {
                    return {
                        file: serializeUploadAttributeValue(attributes.file),
                    }
                },
            },
            legend: {
                default: null,
            },
            /**
             * @type File
             */
            nativeFile: {
                default: null,
                rendered: false,
            },
            isNew: {
                default: false,
                rendered: false,
            },
            notFound: {
                default: false,
                rendered: false,
            },
            isImage: {
                default: false,
                parseHTML: element => element.matches('x-sharp-image'),
                rendered: false,
            },
        }
    },

    parseHTML() {
        return [
            {
                tag: 'x-sharp-image',
            },
            {
                tag: 'x-sharp-file',
            },
        ]
    },

    renderHTML({ node, HTMLAttributes }) {
        if(node.attrs.isImage) {
            return ['x-sharp-image', HTMLAttributes];
        }
        return ['x-sharp-file', HTMLAttributes];
    },

    addProseMirrorPlugins() {
        return [
            getEventsPlugin(this.editor),
        ]
    },

    addCommands(): AnyCommands {
        return {
            insertUpload: () => ({ commands, tr }) => {
                return commands
                    .insertContentAt(tr.selection.to, {
                        type: this.name,
                        attrs: {
                            isNew: true,
                        },
                    });
                // return commands
                //     .insertContentAt(pos ?? tr.selection.to, {
                //         type: this.name,
                //         attrs: {
                //             nativeFile: file,
                //             isImage: file.type.match(/^image\//),
                //         },
                //     });
            },
        }
    },

    addNodeView() {
        return VueNodeViewRenderer(UploadNode);
    },
});
