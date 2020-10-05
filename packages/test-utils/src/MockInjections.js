import Vue from 'vue';
import axios from 'axios';


const injectedCustoms = () => ({
    axiosInstance: axios.create(),
    params: {}
});


const injectedComponents = {
    $field:{},
    $form:{
        data: () => ({
            entityKey: 'entityKey',
            instanceId: 'instanceId',
            errors: {},
            downloadLinkBase:'',
            localized: false,
            locales: null,
        }),
        methods: {
            setUploading() {},
            hasUploadingFields() {}
        },
        created() {
            this.setUploading = jest.fn();
        },
    },
    $tab:{},
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