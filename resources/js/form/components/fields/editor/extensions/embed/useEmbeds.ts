import { FormFieldProps } from "@/form/components/types";
import { EmbedData, FormEditorFieldData } from "@/types";
import { Embed } from "@/form/components/fields/editor/extensions/embed/Embed";
import { AnyCommands, Extension } from "@tiptap/core";
import { getCurrentInstance, nextTick, provide, watch } from "vue";
import { EmbedManager } from "@/embeds/EmbedManager";


export function useEmbeds(
    props: FormFieldProps<FormEditorFieldData>,
    embedManager: EmbedManager,
    content: string
) {
    if(Object.keys(props.field.embeds ?? {}).length === 0) {
        return { extensions: [] };
    }

    provide('embeds', embedManager);

    const updatedContent = embedManager.withEmbedUniqueId(content);

    embedManager.resolveEmbeds(updatedContent);

    return {
        extensions: [
            Extension.create({
                name: 'initEmbeds',
                onBeforeCreate() {
                    this.editor.setOptions({
                        content: updatedContent,
                    });
                },
                addCommands(): AnyCommands {
                    return {
                        insertEmbed: ({ embed }: { embed: EmbedData }) => ({ commands, tr }) => {
                            return commands
                                .insertContentAt(tr.selection.to, {
                                    type: `embed:${embed.key}`,
                                    attrs: {
                                        isNew: true,
                                        'data-unique-id': embedManager.newId(embed)
                                    },
                                });
                        },
                    }
                },
            }),
            ...Object.values(props.field.embeds)
                .filter(Boolean)
                .map((embed) => {
                    return Embed.extend({ name: `embed:${embed.key}` })
                        .configure({ embed: {...embed} })
                }),
        ]
    };
}
