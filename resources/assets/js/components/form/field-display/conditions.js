import * as util from '../../../util';

const helpers = {
    computeSelectCondition({ condValues, fieldValue, isSingleSelect }) {
        if (Array.isArray(condValues)) {
            return isSingleSelect
                ? condValues.some(val => val == fieldValue)
                : condValues.some(cval => fieldValue.some(fval => fval == cval));
        }
        // 'values' is a string
        if (condValues[0] === '!') {
            let condVal = condValues.substring(1);
            return isSingleSelect
                ? condVal != fieldValue
                : !fieldValue.some(fval => fval == condVal);
        }
        // 'values' is not negative
        return isSingleSelect ? condValues == fieldValue : fieldValue.some(fval => fval == condValues);
    }
};

function computeCondition(fields, data, condition) {
    let res = true;

    let { operator } = condition;

    if(operator !== 'or' && operator !== 'and') {
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


        if(field.type === 'autocomplete' || field.type === 'select' || field.type === 'tags') {
            res = helpers.computeSelectCondition({
                condValues: condField.values,
                fieldValue: field.type === 'autocomplete' ? value && value.id : value,
                isSingleSelect: field.type === 'select' && !field.multiple || field.type === 'autocomplete'
            });
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
}

export {
    helpers, computeCondition
}