import { computed, ComputedRef, Ref } from "vue";
import { Editor } from "@tiptap/vue-3";
import { FormEditorFieldData } from "@/types";
import { FormFieldProps } from "@/form/components/types";
import { useForm } from "@/form/useForm";


export function useLocalizedEditor(
    props: FormFieldProps<FormEditorFieldData>,
    createEditor: (content: string | null) => Editor
): ComputedRef<Editor> {
    if(props.field.localized) {
        const form = useForm();
        const localizedEditors = Object.fromEntries(
            form.locales.map(locale => [
                locale,
                createEditor(props.value?.text?.[locale] ?? null),
            ])
        );

        return computed(() => localizedEditors[props.locale]);
    }

    const editor = createEditor(props.value?.text as string);

    return computed(() => editor);
}
