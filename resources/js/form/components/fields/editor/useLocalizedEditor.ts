import { computed, ComputedRef, Ref } from "vue";
import { Editor } from "@tiptap/vue-3";
import { FormEditorFieldData } from "@/types";
import { useParentForm } from "@/form/useParentForm";
import { FormFieldProps } from "@/form/types";


export function useLocalizedEditor(
    props: FormFieldProps<FormEditorFieldData>,
    createEditor: (locale?: string) => Editor
): ComputedRef<Editor> {
    if(props.field.localized) {
        const form = useParentForm();
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
