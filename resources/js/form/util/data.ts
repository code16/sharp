import { hasDependency } from './dynamic-attributes';
import { FormData } from "@/types";

export function getDependantFieldsResetData(fields: FormData['fields'], key: string, transformValue = null) {
    return Object.values(fields)
        .filter(field => {
            if('callbackLinkedFields' in field) {
                return field.callbackLinkedFields?.includes(key);
            }
            if('dynamicAttributes' in field) {
                return hasDependency(key, field.dynamicAttributes, field);
            }
            return false;
        })
        .reduce((res, field) => ({
            ...res,
            [field.key]: transformValue
                ? transformValue(field, null)
                : null,
        }), {});
}
