import 'core-js/modules/es7.object.entries';

export default {
    install(Vue, { customFields }) {
        Object.entries(customFields).forEach(([name, field])=>{
            Vue.component(`SharpCustomField_${name}`, field);
        });
    }
};