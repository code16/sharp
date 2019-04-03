import { getDynamicAttributeOptions } from "../components/form/dynamic-attributes/util";

export function setDefaultValue(field, attribute, callback) {
    const dynamicOptions = getDynamicAttributeOptions(field.dynamicAttributes, attribute);
    if(dynamicOptions) {
        field.$nextTick(callback);
    } else {
        callback();
    }
}