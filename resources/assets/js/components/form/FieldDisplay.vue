<script>
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
                return isSingleSelect
                    ? values == value
                    : !value.includes(condField.values.substring(1));
            }
            return false;
        }
        // 'values' is not negative
        if (value && value.length) {
            return value.includes(condField.values)
        }
        return true;
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

            if(!value) {
                res = true;
                continue;
            }

            if(field.type === 'autocomplete' || field.type === 'select' || field.type === 'taginput') {
                let isSingleSelect = field.type === 'select' && !field.multiple;
                res = computeSelectCondition(condField.values, value, isSingleSelect);
            }
            else if(field.type === 'check') {
                if(typeof value !== "boolean") {
                    util.error(`Conditional display : 'values' must be a boolean for a 'check' field ('${condField.key}')`,condition,field);
                    res = true;
                }
                else res = value === condField.values;
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

    export default {
        name: 'SharpFieldDisplay',
        functional: true,

//        props: {
//            fieldKey: String,
//            contextFields:Object,
//            contextData:Object,
//             ... callbacks
//        },

        render(h, { props }) {
            let { fieldKey, contextFields, contextData, ...sharedProps } = props;
            let field = contextFields[fieldKey];

            contextData = (contextData||{});

            if(!(fieldKey in contextFields)) {
                util.error(`Field displayer ('layout') : Can't find a field with key '${fieldKey}' in 'fields'`,contextFields);
                return null;
            }
            /*else if(!(fieldKey in contextData)) {
                util.error(`Field displayer : Can't find key '${fieldKey}' in 'data'`,contextData);
                return null;
            }*/

            return acceptCondition(contextFields, contextData, field.conditionalDisplay) ?
                h(FieldContainer,{
                    props: {
                        fieldKey,
                        fieldProps: field,
                        fieldType: field.type,
                        value: contextData[fieldKey],
                        label: field.label,
                        helpMessage: field.helpMessage,
                        ...sharedProps
                    }
                }) : null;
        }
    }
</script>