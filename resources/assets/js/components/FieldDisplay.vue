<script>
    import util from '../util';
    import FieldContainer from './FieldContainer';

    const acceptCondition = (fields, data, condition) => {
        if(!condition)
            return true;
        if(!(condition.key in fields)) {
            util.error(`Conditional display : can't find a field with key '${condition.key}' in 'fields'`, condition);
            return true;
        }
        let { not, key, values, operator } = condition;
        let field = fields[key];
        let res = true;
        let value = data[key];

        if(field.type === 'autocomplete' || field.type === 'select') {
            if(!operator || operator==='or') {
                res = values.includes(value);
            }
            else if(operator==='and') {
                if(field.type !== 'select' || field.type === 'select' && !field.multiple) {
                    util.error(`Conditional display : Unable to process operator 'and' for field \`${key}\` (only available with multiple select)`, condition, field);
                    return;
                }
                let arrayValue = value;
                res = values.every(v=>arrayValue.includes(v));
            }
            else {
                util.error(`Conditional display : unknown operator '${operator}'`, condition);
                return true;
            }
        }
        else if(field.type === 'check')
            res = value;
        else
            return true;

        return not ? !res : res;
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

            if(!(fieldKey in contextFields)) {
                util.error(`Field displayer : Can't find a field with key '${fieldKey}' in 'fields'`,contextFields);
                return null;
            }
            else if(!(fieldKey in contextData)) {
                util.error(`Field displayer : Can't find key '${fieldKey}' in 'data'`,contextData);
                return null;
            }

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