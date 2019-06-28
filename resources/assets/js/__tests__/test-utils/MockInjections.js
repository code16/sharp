import Vue from 'vue';
import axios from 'axios';


const injectedCustoms = () => ({
    xsrfToken: 'xsrfTest',
    axiosInstance: axios.create(),
    params: {}
});


const injectedComponents = {
    $field:{},
    $form:{
        data: () => ({
            errors: {},
            downloadLinkBase:'',
            localized: false
        })
    },
    $tab:{},

    actionsBus:{},
    mainLoading:{}
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
            ...injectedCustoms()
        };
    }
}