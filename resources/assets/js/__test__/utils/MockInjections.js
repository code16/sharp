import Vue from 'vue';

const injectedComponents = {
    $field:{},
    $form:{
        data: () => ({
            errors: {}
        })
    },
    $tab:{}
};

export default {
    provide() {
        return Object.keys(injectedComponents).reduce((injections, compName) => {
            let Comp = Vue.extend(injectedComponents[compName]);
            injections[compName] = new Comp();
            return injections;
        }, {});
    }
}