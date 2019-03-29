import { error } from "../../../util";

export function getEmptySources({ contextSources, contextData }) {
    return contextSources.filter(sourceKey => {
        const value = contextData[sourceKey];
        return value == null || value === '';
    });
}

export function warnEmptyValue(emptySources, { sourceName, attributeName }) {
    emptySources.forEach(sourceKey => {
        error(`Dynamic attribute '${attributeName}' of the field '${sourceName}' has invalid source:
               The field '${sourceKey}' has value null, undefined or empty string.\n 
               Ensure the field not clearable and has initial default value.\n
               You can also define a default value for the attribute.`);
    });
}