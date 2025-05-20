import { computed, ComputedRef } from "vue";
import { Editor } from "@tiptap/vue-3";
import { FormEditorFieldData } from "@/types";
import { useParentForm } from "@/form/useParentForm";
import { FormFieldProps } from "@/form/types";


export function useLocalizedEditor(
    props: FormFieldProps<FormEditorFieldData>,
    createEditor: (locale?: string) => Editor
): ComputedRef<Editor> {
    const form = useParentForm();

    if(props.field.localized && form.locales?.length) {
        const localizedEditors = Object.fromEntries(
            form.locales.map(locale => [
                locale,
                createEditor(locale),
            ])
        );

        return computed(() => localizedEditors[props.locale]);
    }

    const editor = createEditor();

    return computed(() => editor);
}
