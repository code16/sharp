import { hasDependency } from './dynamic-attributes';
import { getDynamicAttributeOptions } from './dynamic-attributes/util';

export function getDependantFieldsResetData(fields, key, transformValue) {
    return Object.values(fields)
        .filter(field => hasDependency(key, field.dynamicAttributes, field))
        .reduce((res, field) => ({
            ...res,
            [field.key]: transformValue
                ? transformValue(field, null)
                : null,
        }), {});
}

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