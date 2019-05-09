import { hasDependency } from "../../components/form/dynamic-attributes";


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

export {
    transformFields
} from './transform-fields';