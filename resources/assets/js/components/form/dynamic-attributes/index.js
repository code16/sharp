import { resolveValue } from "./resolve";
import { getContextSources } from "./resolve";


export function hasDependency(sourceKey, dynamicAttributes, attributes) {
    return (dynamicAttributes || []).some(options => {
        const attributeValue = attributes[options.name];
        const contextSources = getContextSources({
            dynamicOptions: options,
            attributeValue,
        });
        return contextSources.includes(sourceKey);
    });
}

export function transformAttributes(attributes, dynamicAttributes, contextData) {
    const transformedData = Object.entries(attributes || {})
        .reduce((res, [attributeName, attributeValue]) => {
            const resolvedData = resolveValue(attributeName, attributeValue, {
                dynamicAttributes,
                contextData,
            });
            const emptyAttrs = res.resolvedEmptyAttributes || [];

            if(resolvedData.isEmpty) {
                emptyAttrs.push(attributeName);
            }

            return {
                ...res,
                resolvedEmptyAttributes: emptyAttrs,
                attributes: {
                    ...res.attributes,
                    [attributeName]: resolvedData.value,
                },
            };
        }, {});

    return {
        attributes: transformedData.attributes,
        resolvedEmptyAttributes: transformedData.resolvedEmptyAttributes,
    };
}