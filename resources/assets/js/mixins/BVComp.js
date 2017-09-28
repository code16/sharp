import Vue from 'vue';
import BootstrapVue from 'bootstrap-vue';

Vue.use(BootstrapVue);

export default function BVComp(compName) {
    return Vue.options.components[compName].options;
}
