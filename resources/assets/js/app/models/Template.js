import util from '../../util';

class Template {
    constructor(fieldKey, templateName) {
        this.fieldKey = fieldKey;
        this.name = templateName;
    }

    get name() { return this._name }
    get fieldKey() { return this._fieldKey }

    set name(name) {
        this._name=util.capitalize(name);
    }
    set fieldKey(fieldKey) {
        this._fieldKey=util.capitalize(fieldKey);
    }

    get compName() {
        return util.hyphenate(`SharpTemplate${this.name}${this.fieldKey}`);
    }

    get exists() {
        return !!Vue.options.components[this.compName];
    }

    get compNameOrDefault() {
        return this.exists ? this.compName : null;
    }

// static utils

    static isTemplateProp(fieldPropName) {
        return /^.+Template$/.test(fieldPropName);
    }

    static parseTemplateName(fieldPropName) {
        let result=/^(.+)Template$/.exec(fieldPropName);
        if(result.length>1)
            return result[1];
        return null;
    }
}

export default Template;