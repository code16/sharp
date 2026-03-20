import { FormFieldProps } from "@/form/types";
import { FormListFieldData } from "@/types";
import { inject } from "vue";
import { Form } from "@/form/Form";

export type ParentListField = {
    props: FormFieldProps<FormListFieldData>,
    form: Form,
}

/**
 * @see import('./List.vue')
 */
export function useParentListField() {
    return inject<ParentListField>('listField', null);
}
