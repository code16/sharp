import Template from '../models/Template';
import TemplateDefinition from '../../template-definition';

import util from '../../util';

class TemplateController {
    /**
     * 
     * @param {*} fieldKey Value of the property `key` of the field (ex: city)
     * @param templateName
     * @param templateValue
     * @param templateProps
     */
    static compileAndRegisterComponent(fieldKey, { templateName, templateValue, templateProps })  {

        let template = new Template(fieldKey, Template.parseTemplateName(templateName));
        let compName = template.compName;

        if(!template.exists) {
            /*
            let definition = TemplateDefinition[name];
        
            let mixins = [];
            let wrapper = 'div';


            if(definition) {
                mixins.push(definition);
                if(definition.wrapIn)
                    wrapper = definition.wrapIn;
                else
                    util.log(`wrapper element (\`wrapIn\`) is not set in '${name}' definition (default div)`);
            }
            else {
                util.warn(`'${name}' doesn't match any definition`);
            }*/

            Vue.component(compName, {
                template: `<div>${templateValue}</div>`,
                props: templateProps
            });
        }

        return compName;
    }
}

export default TemplateController;