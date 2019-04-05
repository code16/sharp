import { transformAttributes } from "../../components/form/dynamic-attributes";


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
            [fieldKey]: serializeValue(field.type, value),
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