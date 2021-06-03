import { lang } from "sharp";


export function validateTextField(value, { maxlength }) {
    if(maxlength && value?.length > maxlength) {
        return lang('form.text.validation.maxlength').replace(':maxlength',maxlength);
    }
    return null;
}
