//
//

class Template {
    constructor(fieldKey, templateName) {
        this.fieldKey = fieldKey;
        this.templateName = templateName;
    }

    get compName() {
        return `sharp-template-${this.templateName}-${this.fieldKey}`;
    }

    get exists() {
        return !!Vue.options.components[this.compName];
    }

    get compNameOrDefault() {
        return this.exists ? this.compName: null;
    }
}



export default {
    methods: {
        template: function(fieldKey, templateName) {
            return new Template(this.fieldKey || fieldKey,
                                this.templateName || templateName);
        }
    }
};