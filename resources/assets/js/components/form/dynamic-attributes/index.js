import get from 'lodash/get';
import buildTemplate from 'lodash/template';
import { warnEmptyValue, getEmptySources } from "./util";


const interpolateRE = /{{([\s\S]+?)}}/g;

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
    const defaultData = (sources||[]).reduce((res, [key]) => ({ ...res, [key]: null, }), {});
    return compile({
        ...defaultData,
        ...contextData,
    });
}

export function resolveValue({ sourceName, attributeName, attributeValue, dynamicAttributes, contextData }) {
    const dynamicOptions = getDynamicAttributeOptions(dynamicAttributes, attributeName) || {};
    if(dynamicOptions.type === 'map') {
        const contextSources = dynamicOptions.path;
        const emptySources = getEmptySources({ contextSources, contextData });
        if(emptySources.length > 0) {
            warnEmptyValue(emptySources, { sourceName, attributeName });
            return dynamicOptions.default;
        }
        return getValueFromMap({
            map: attributeValue,
            path: dynamicOptions.path,
            contextData,
        });
    } else if(dynamicOptions.type === 'template') {
        const template = attributeValue;
        const contextSources = getSourcesFromTemplate(template);
        const emptySources = getEmptySources({ contextSources, contextData });
        if(emptySources.length > 0) {
            warnEmptyValue(emptySources, { sourceName, attributeName });
            return dynamicOptions.default;
        }
        return getValueFromTemplate({
            template,
            sources: contextSources,
            contextData,
        });
    }
    return attributeValue;
}

export function transformAttributes(props, contextData) {
    const { key, dynamicAttributes, ...attributes } = props;
    return Object.entries(attributes || {})
        .reduce((res, [attributeName, attributeValue]) => ({
            ...res,
            [attributeName]: resolveValue({
                attributeName,
                attributeValue,
                dynamicAttributes,
                contextData,
            }),
        }), {});
}