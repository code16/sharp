import { logError } from 'sharp';
import { UnknownField } from "sharp/components";
import FieldContainer from './ui/FieldContainer';
import { isLocalizableValueField } from "../util/locale";
import { computeCondition } from '../util/conditional-display';
import { isArray } from "axios/lib/utils";


export function acceptCondition (fields, data, condition) {
    if(!condition)
        return true;

    return computeCondition(fields,data,condition);
}

const getValue = (form, field, value, locale) => {
    if(form.localized && field.localized && value && isLocalizableValueField(field)) {
        if(typeof value !== 'object' || isArray(value)) {
            logError(`Localized field '${field.key}' value must be a object, given :`, JSON.stringify(value));
            return value;
        }
        return value[locale];
    }

    return value;
};


export default {
    name: 'SharpFieldDisplay',
    functional: true,

    inject:['$form'],



    render(h, { props, injections, data }) {
        let { fieldKey,
            contextFields,
            contextData,
            errorIdentifier,
            updateVisibility,
            readOnly,
            ...sharedProps } = props;

        let { $form } = injections;

        let field = contextFields[fieldKey];
        let value = contextData[fieldKey];

        if(!(fieldKey in contextFields)) {
            logError(`Field display ('layout') : Can't find a field with key '${fieldKey}' in 'fields'`,contextFields);
            return h(UnknownField, { props: { name: fieldKey } });
        }

        let isVisible = acceptCondition(contextFields, contextData, field.conditionalDisplay);

        updateVisibility && updateVisibility(fieldKey, isVisible);

        return isVisible ? h(FieldContainer, {
            ...data,
            attrs: {
                fieldKey,
                fieldProps: {
                    ...field,
                    readOnly: readOnly || field.readOnly,
                },
                fieldType: field.type,
                value: getValue($form, field, value, props.locale),
                originalValue: value,
                label: field.label,
                helpMessage: field.helpMessage,
                errorIdentifier,
                localizedErrorIdentifier: field.localized
                    ? `${errorIdentifier}.${props.locale}`
                    : null,
                ...sharedProps
            }
        }) : null

    }
}
