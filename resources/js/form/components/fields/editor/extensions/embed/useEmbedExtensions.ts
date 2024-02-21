import { FormFieldProps } from "@/form/components/types";
import { FormEditorFieldData } from "@/types";
import { Embed } from "@/form/components/fields/editor/extensions/embed/embed";
import { Extension } from "@tiptap/core";
import { provide } from "vue";
import { EmbedManager } from "@/form/components/fields/editor/extensions/embed/EmbedManager";



export function useEmbedExtensions(
    props: FormFieldProps<FormEditorFieldData>,
    embeds: EmbedManager
) {
    provide('embeds', embeds);

    return [
        Extension.create({
            onCreate() {
                embeds.resolveAll();
            }
        }),
        Object.values({ ...props.field.embeds, upload: null })
            .filter(Boolean)
            .map((embed) => {
                Embed.extend({ name: `embed:${embed.key}` })
                    .configure({ embed })
            }),
    ];
}
