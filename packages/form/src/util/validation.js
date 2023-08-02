import { __ } from "@/util/i18n";

export function validateTextField(value, { maxlength }) {
    if(maxlength && value?.length > maxlength) {
        return __('sharp::form.text.validation.maxlength', { maxlength });
    }
    return null;
}
