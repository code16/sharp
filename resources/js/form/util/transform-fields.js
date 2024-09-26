import { transformAttributes } from "./dynamic-attributes";


function serializeValue(fieldType, value) {
    if(fieldType === 'autocomplete') {
        return !!value ? value.id : value;
    }
    return value;
}

function serializeData(data, fields) {
    return Object.entries(data).reduce((res, [fieldKey, value]) => {
        const field = fields[fieldKey];
        return {
            ...res,
            [fieldKey]: field
                ? serializeValue(field.type, value)
                : null,
        }
    }, {});
}

export function transformFields(fields, data) {
    const serializedData = serializeData(data, fields);
    return Object.entries(fields).reduce((res, [fieldKey, field]) => {
        const {
            attributes,
            resolvedEmptyAttributes,
        } = transformAttributes(field, field.dynamicAttributes, serializedData);

        const readOnly = attributes.readOnly || resolvedEmptyAttributes.length > 0;

        return {
            ...res,
            [fieldKey]: {
                ...attributes,
                readOnly,
            },
        };
    }, {});
}
