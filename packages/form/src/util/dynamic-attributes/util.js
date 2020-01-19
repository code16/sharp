import get from 'lodash/get';
import buildTemplate from 'lodash/template';
import { logError } from "sharp";

const interpolateRE = /{{([\s\S]+?)}}/g;

export function getEmptyValueSources({ contextSources, contextData }) {
    return contextSources.filter(sourceKey => {
        const value = contextData[sourceKey];
        return value == null || value === '';
    });
}

export function warnEmptyValue(emptySources, { sourceName, attributeName }) {
    emptySources.forEach(sourceKey => {
        logError(`Dynamic attribute '${attributeName}' of the field '${sourceName}' has invalid source:
                  The field '${sourceKey}' has value null, undefined or empty string.\n 
                  Ensure the field not clearable and has initial default value.\n
                  You can also define a default value for the attribute.`);
    });
}

export function getDynamicAttributeOptions(dynamicAttributes, attributeName) {
    return (dynamicAttributes || [])
        .find(option => option.name === attributeName);
}

export function getValueFromMap({ map, path, contextData }) {
    const valuePath = path.map(key => contextData[key]);
    return get(map, valuePath);
}

export function getSourcesFromTemplate(template) {
    return [...template.matchAll(interpolateRE)].map(match => match[1].trim());
}

export function getValueFromTemplate({ template, sources, contextData }) {
    const compile = buildTemplate(template, {
        interpolate: interpolateRE,
        evaluate: false,
        escape: false,
    });
    const defaultData = (sources||[]).reduce((res, key) => ({ ...res, [key]: null, }), {});
    return compile({
        ...defaultData,
        ...contextData,
    });
}