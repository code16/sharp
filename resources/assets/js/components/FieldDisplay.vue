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
        name: 'SharpFieldDisplayer',
        functional: true,

        render(h, { props }) {
            let { fieldKey, fields, data, ...sharedProps } = props;
            let field = fields[fieldKey];

            if(!(fieldKey in fields)) {
                util.error(`Field displayer : Can't find a field with key '${fieldKey}' in 'fields'`,fields);
                return null;
            }
            else if(!(fieldKey in data)) {
                util.error(`Field displayer : Can't find key '${fieldKey}' in 'data'`,fields);
                return null;
            }

            return acceptCondition(fields, data, field.conditionalDisplay) ?
                   h(FieldContainer.name,{
                        props: {
                            fieldKey,
                            fieldProps: field,
                            fieldType: field.type,
                            value: data[fieldKey],
                            label: field.label,
                            helpMessage: field.helpMessage,
                            readOnly: field.readOnly,
                            ...sharedProps
                        }
                   }) : null;
        }
    }
</script>