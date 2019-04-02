import {
    getEmptyValueSources,
    warnEmptyValue,
    getDynamicAttributeOptions,
    getSourcesFromTemplate,
    getValueFromMap,
    getValueFromTemplate,
} from './util';


export function getContextSources({ dynamicOptions={}, attributeValue }) {
    if(dynamicOptions.type === 'map') {
        return dynamicOptions.path;
    } else if(dynamicOptions.type === 'template') {
        return getSourcesFromTemplate(attributeValue);
    } else {
        return [];
    }
}

export function resolveDynamicValue({ dynamicOptions, attributeValue, contextData, contextSources }) {
    if(dynamicOptions.type === 'map') {
        return getValueFromMap({
            map: attributeValue,
            path: dynamicOptions.path,
            contextData,
        });
    } else if(dynamicOptions.type === 'template') {
        return getValueFromTemplate({
            template: attributeValue,
            sources: contextSources,
            contextData,
        });
    } else {
        return attributeValue;
    }
}

export function resolveValue(attributeName, attributeValue, { dynamicAttributes, contextData }) {
    const dynamicOptions = getDynamicAttributeOptions(dynamicAttributes, attributeName);
    const contextSources = getContextSources({ dynamicOptions, attributeValue });

    if(!dynamicOptions) {
        return {
            value: attributeValue,
        };
    }

    const emptyValueSources = getEmptyValueSources({ contextSources, contextData });
    if(emptyValueSources.length > 0) {
        // warnEmptyValue(emptyValueSources, { sourceName, attributeName });
        return {
            isDefault: true,
            value: dynamicOptions.default,
        };
    }

    return {
        value: resolveDynamicValue({ dynamicOptions, attributeValue, contextData, contextSources }),
    }
}