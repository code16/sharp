import Vue from 'vue';

const injectedComponents = ['$field', '$form'];

export default {
    provide() {
        return injectedComponents.reduce((injections, prop) => {
            injections[prop] = new Vue();
            return injections;
        }, {});
    }
}