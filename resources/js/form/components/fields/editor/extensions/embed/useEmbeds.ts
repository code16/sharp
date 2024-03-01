import { EmbedData, FormEditorFieldData } from "@/types";
import { Embed } from "@/form/components/fields/editor/extensions/embed/Embed";
import { AnyCommands, Extension } from "@tiptap/core";
import { getCurrentInstance, nextTick, provide, watch } from "vue";
import { ContentEmbedManager } from "@/content/ContentEmbedManager";
import { FormFieldProps } from "@/form/types";



export function useEmbeds(
    props: FormFieldProps<FormEditorFieldData>,
    embedManager: ContentEmbedManager,
    content: string
) {
    if(Object.keys(props.field.embeds ?? {}).length === 0) {
        return { extensions: [] };
    }

    provide('embeds', embedManager);

    const updatedContent = embedManager.withEmbedUniqueId(content);

    embedManager.resolveContentEmbeds(updatedContent);

    return {
        extensions: [
            Extension.create({
                name: 'initEmbeds',
                onBeforeCreate() {
                    this.editor.setOptions({
                        content: updatedContent,
                    });
                },
            }),
            ...Object.values(props.field.embeds)
                .filter(Boolean)
                .map((embed) => {
                    return Embed.extend({
                        name: `embed:${embed.key}`,
                        addOptions() {
                            return { embed, embedManager }
                        }
                    })
                }),
        ]
    };
}
