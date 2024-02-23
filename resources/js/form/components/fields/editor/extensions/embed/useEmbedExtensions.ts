import { FormFieldProps } from "@/form/components/types";
import { FormEditorFieldData } from "@/types";
import { Embed } from "@/form/components/fields/editor/extensions/embed/Embed";
import { Extension } from "@tiptap/core";
import { provide } from "vue";
import { EmbedManager } from "@/form/components/fields/editor/extensions/embed/EmbedManager";



export function useEmbedExtensions(
    props: FormFieldProps<FormEditorFieldData>,
    embeds: EmbedManager
) {
    if(!props.field.embeds?.length) {
        return [];
    }

    provide('embeds', embeds);

    return [
        Extension.create({
            onCreate() {
                embeds.editorCreated = true;
                embeds.resolveAllInitialContentEmbeds();
            }
        }),
        Object.values(props.field.embeds)
            .filter(Boolean)
            .map((embed) => {
                Embed.extend({ name: `embed:${embed.key}` })
                    .configure({ embed })
            }),
    ];
}
