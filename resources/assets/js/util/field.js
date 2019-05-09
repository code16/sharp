import { getDynamicAttributeOptions } from "../components/form/dynamic-attributes/util";

export function setDefaultValue(field, callback, { dependantAttributes } = {}) {
    const dynamicAttributes = field.$attrs.dynamicAttributes;
    const hasDynamicAttributes = (dependantAttributes || [])
        .some(attrName => getDynamicAttributeOptions(dynamicAttributes, attrName));

    if(hasDynamicAttributes) {
        field.$nextTick(callback);
    } else {
        callback();
    }
}