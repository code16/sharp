import { computed } from "vue";
import { isSelected } from "@/form/util/select";
import { __ } from "@/utils/i18n";
import { FormFieldEmits, FormFieldProps } from "@/form/types";
import { FormSelectFieldData } from "@/types";


export function useSelect(props: FormFieldProps<FormSelectFieldData>, emit: FormFieldEmits<FormSelectFieldData>) {
    function validate(value: typeof props.value) {
        if(props.field.multiple && props.field.maxSelected && (value as Array<number | string>)?.length > props.field.maxSelected) {
            return __('sharp::form.select.validation.max_selected', { max_selected: props.field.maxSelected });
        }
        return null;
    }

    const isAllSelected = computed(() => props.field.options?.every(option => (props.value as Array<number | string>)?.some(v => isSelected(option, v))))
    function selectAll() {
        const value = props.field.options.map(o => o.id);
        emit('input', value, { error: validate(value) });
    }

    return {
        validate,
        isAllSelected,
        selectAll,
    }
}
