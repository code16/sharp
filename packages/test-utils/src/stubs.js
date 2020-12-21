import Vue from 'vue';

const stubRE = /^<([a-z\-]+-stub)/;

export function createStub(options) {
    const match = options.template?.match(stubRE);
    if(match) {
        Vue.config.ignoredElements.push(match[1]);
    }
    return options;
}
