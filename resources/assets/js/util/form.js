import { hasDependency, transformAttributes } from "../components/form/dynamic-attributes";


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

export function transformFields(fields, data) {
    return Object.entries(fields).reduce((res, [fieldKey, field]) => {
        const {
            attributes,
            resolvedEmptyAttributes,
        } = transformAttributes(field, field.dynamicAttributes, data);

        const readOnly = resolvedEmptyAttributes.length > 0;

        return {
            ...res,
            [fieldKey]: {
                ...attributes,
                readOnly,
            },
        };
    }, {});
}