import Template from '../models/Template';
import TemplateDefinition from '../../template-definition';

import util from '../../util';

class TemplateController {
    /**
     * 
     * @param {*} fieldKey Value of the property `key` of the field (ex: city)
     * @param {*} fieldPropName Name of the current evaluated field Property (ex: listItemTemplate)
     * @param {*} fieldPropValue Value of the current evaluated field Property (ex: `<div>{{city}}</div>`)
     */
    static compileAndRegisterComponent(fieldKey, fieldPropName, fieldPropValue)  {
        if(!Template.isTemplateProp(fieldPropName)) 
            return;

        let template = new Template(fieldKey, Template.parseTemplateName(fieldPropName));
        let compName = template.compName;

        if(!template.exists) {
            let definition = TemplateDefinition[fieldPropName];
        
            let mixins = [];
            let wrapper = 'div';

            if(definition) {
                mixins.push(definition);
                if(definition.wrapIn)
                    wrapper = definition.wrapIn;
                else
                    util.log(`wrapper element (\`wrapIn\`) is not set in '${fieldPropName}' definition (default div)`);
            }
            else {
                util.warn(`'${fieldPropName}' doesn't match any definition`);
            }

            Vue.component(compName, {
                mixins,
                mounted() {
                    if(definition.propagateEvents) {
                        for(let event of definition.propagateEvents) {
                            this.$el.addEventListener(event, ()=>{
                                this.$emit(event);
                            }, false);
                        }
                    }
                },
                ...Vue.compile(`<${wrapper}>${fieldPropValue}</${wrapper}>`)
            });
        }

        return compName;
    }
}

export default TemplateController;