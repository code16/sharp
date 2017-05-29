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
    static compileAndRegisterComponent(fieldKey, { templateName, templateValue })  {

        let template = new Template(fieldKey, Template.parseTemplateName(templateName));
        let compName = template.compName;

        if(!template.exists) {

            let definition = TemplateDefinition[templateName];


            Vue.component(compName, {
                functional:true,
                render(h, ctx) {
                    return h({
                        mixins: [definition||{}],
                        template:`<div>${templateValue}</div>`,
                        props: Object.keys(ctx.props)
                    }, ctx.data);
                }
            });
        }

        return compName;
    }
}

export default TemplateController;