import util from '../../util';
import FieldContainer from './FieldContainer';

const computeSelectCondition = (condValues, fieldValue, isSingleSelect) => {
    if (Array.isArray(condValues)) {
        return isSingleSelect
            ? condValues.some(val => val == fieldValue)
            : condValues.some(val => fieldValue.includes(val));
    }
    // 'values' is a string
    if (condValues[0] === '!') {
        if (fieldValue && fieldValue.length) {
            let condVal = condValues.substring(1);
            return isSingleSelect
                ? condVal != fieldValue
                : !fieldValue.includes(condVal);
        }
        return false;
    }
    // 'values' is not negative
    if (fieldValue && fieldValue.length) {
        return isSingleSelect ? condValues === fieldValue : fieldValue.includes(condValues);
    }
    return false;
};

const computeCondition = (fields, data, condition) => {
    let res = true;

    let { operator } = condition;

    if(!(operator==='or' || operator==='and')) {
        util.error(`Conditional display : unknown operator '${operator}'`, condition);
        return true;
    }

    for(let condField of condition.fields) {
        if(!(condField.key in fields)) {
            util.error(`Conditional display : can't find a field with key '${condition.key}' in 'fields'`, condition);
            res = true;
        }

        let field = fields[condField.key];
        let value = data[condField.key];


        if(field.type === 'autocomplete' || field.type === 'select' || field.type === 'taginput') {
            let isSingleSelect = field.type === 'select' && !field.multiple || field.type === 'autocomplete';
            res = computeSelectCondition(condField.values, value, isSingleSelect);
        }
        else if(field.type === 'check') {
            if(typeof condField.values !== "boolean") {
                util.error(`Conditional display : 'values' must be a boolean for a 'check' field ('${condField.key}')`,condition,field);
                res = true;
            }
            else res = (value == condField.values);
        }
        else {
            util.error(`Conditional display : unprocessable field type '${field.type}'`, field);
            res = true;
        }

        if(operator==='and' && !res)
            return false;
        if(operator==='or' && res)
            return true;
    }
    return res;
};

const acceptCondition = (fields, data, condition) => {
    if(!condition)
        return true;

    return computeCondition(fields,data,condition);
};

const getValue = (form, field, value, locale) => {

    if(form.localized && field.localized) {
        //console.log(form, field, value, locale);
        return value[locale];
    }

    return value;
};

const getIdentifier = (identifier, field, locale) => {
    if(field.localized)
        return `${identifier}.${locale}`;
    return identifier;
};

export default {
    name: 'SharpFieldDisplay',
    functional: true,

//        props: {
//            fieldKey: String,
//            contextFields:Object,
//            contextData:Object,
//             ... callbacks, error
//        },

    inject:['$form'],

    render(h, { props, injections }) {
        let { fieldKey, contextFields, contextData, errorIdentifier, ...sharedProps } = props;
        let { $form } = injections;

        let field = contextFields[fieldKey];
        let value = contextData[fieldKey];

        if(!(fieldKey in contextFields)) {
            util.error(`Field displayer ('layout') : Can't find a field with key '${fieldKey}' in 'fields'`,contextFields);
            return null;
        }

        return acceptCondition(contextFields, contextData, field.conditionalDisplay) ?
            h(FieldContainer,{
                props: {
                    fieldKey,
                    fieldProps: field,
                    fieldType: field.type,
                    value: getValue($form, field, value, props.locale),
                    label: field.label,
                    helpMessage: field.helpMessage,
                    errorIdentifier: getIdentifier(errorIdentifier, field, props.locale),
                    ...sharedProps
                }
            }) : null;
    }
}
