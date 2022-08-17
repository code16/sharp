import Vue from "vue";


export function ignoreVueElement(tag) {
    if(tag && !Vue.config.ignoredElements.includes(tag)) {
        Vue.config.ignoredElements.push(tag);
    }
}
