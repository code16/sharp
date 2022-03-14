import { Node } from "@tiptap/core";

export const Embed = Node.create({
    name: 'embed',

    atom: true,

    isolating: true,

    priority: 150,

    addOptions: () => ({
        tag: null,
        attributes: {},
        template: null,
    }),

    addAttributes() {
        return Object.fromEntries(
            Object.entries(this.options.attributes)
                .map(([attributeName, options]) => [
                    attributeName, {
                        default: null,
                    }
                ])
        );
    },

    parseHTML() {
        return [
            {
                tag: this.options.tag,
            },
        ]
    }
});
