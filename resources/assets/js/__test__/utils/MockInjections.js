import Vue from 'vue';


const injectedStrings = {
    xsrfToken: 'xsrfTest'
};


const injectedComponents = {
    $field:{},
    $form:{
        data: () => ({
            errors: {}
        })
    },
    $tab:{},

    actionsBus:{}
};

function resolveComponents(comps) {
    return Object.keys(comps).reduce((injections, compName) => {
        let Comp = Vue.extend(comps[compName]);
        injections[compName] = new Comp();
        return injections;
    }, {});
}

export default {
    provide() {
        return {
            ...resolveComponents(injectedComponents),
            ...injectedStrings
        };
    }
}