import { Attribute, Node, NodeViewProps } from "@tiptap/core";

export type ExtensionNodeProps<N extends Node, Attrs> =
    Omit<NodeViewProps, 'extension' | 'node' | 'updateAttributes'> & {
        extension: N,
        node: Omit<NodeViewProps['node'], 'attrs'> & { attrs: Attrs },
        updateAttributes: (attrs: Partial<Attrs>) => void,
    }

export type ExtensionAttributesSpec<Attrs> = {
    [name in keyof Attrs]: Partial<Attribute>
}
