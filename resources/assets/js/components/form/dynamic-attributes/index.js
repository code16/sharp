import { resolveValue } from "./resolve";


export function transformAttributes(attributes, dynamicAttributes, contextData) {
    const transformedData = Object.entries(attributes || {})
        .reduce((res, [attributeName, attributeValue]) => {
            const resolvedData = resolveValue(attributeName, attributeValue, {
                dynamicAttributes,
                contextData,
            });
            const defaults = res.resolvedDefaultAttributes || [];

            if(resolvedData.isDefault) {
                defaults.push(attributeName);
            }

            return {
                ...res,
                resolvedDefaultAttributes: defaults,
                attributes: {
                    ...res.attributes,
                    [attributeName]: resolvedData.value,
                },
            };
        }, {});

    return {
        attributes: transformedData.attributes,
        resolvedDefaultAttributes: transformedData.resolvedDefaultAttributes,
    };
}