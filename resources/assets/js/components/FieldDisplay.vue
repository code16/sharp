<script>
    import FieldContainer from './FieldContainer';
    import util from '../util';

    const acceptCondition = (fields, data, condition) => {
        if(!condition)
            return true;
        if(!(condition.key in fields)) {
            util.error(`Conditional display : can't find a field with key '${condition.key}' in 'fields'`, condition);
            return true;
        }
        let { not, key, values } = condition;
        let field = fields[key];
        let res = true;
        let value = data[key];

        if(field.type === 'autocomplete' || field.type === 'dropdown')
            res = values.includes(value);
        else if(field.type === 'check')
            res = value;
        else
            return true;

        return not ? !res : res;
    };

    export default {
        name: 'SharpFieldDisplay',
        functional: true,

        render(h, { props }) {
            console.log(arguments);
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
                   h(FieldContainer.name,{
                        props: {
                            fieldKey,
                            fieldProps: field,
                            fieldType: field.type,
                            value: contextData[fieldKey],
                            label: field.label,
                            helpMessage: field.helpMessage,
                            readOnly: field.readOnly,
                            ...sharedProps
                        }
                   }) : null;
        }
    }
</script>