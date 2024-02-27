import { FormFieldProps } from "@/form/components/types";
import { FormEditorFieldData } from "@/types";
import { Embed } from "@/form/components/fields/editor/extensions/embed/Embed";
import { Extension } from "@tiptap/core";
import { nextTick, provide, watch } from "vue";
import { EmbedManager } from "@/form/components/fields/editor/extensions/embed/EmbedManager";



export function useEmbedExtensions(
    props: FormFieldProps<FormEditorFieldData>,
    embeds: EmbedManager
) {
    if(Object.keys(props.field.embeds ?? {}).length === 0) {
        return [];
    }

    provide('embeds', embeds);

    return [
        ...Object.values(props.field.embeds)
            .filter(Boolean)
            .map((embed) => {
                return Embed.extend({ name: `embed:${embed.key}` })
                    .configure({ embed: {...embed} })
            }),
    ];
}
