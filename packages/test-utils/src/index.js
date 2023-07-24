export { default as MockInjections } from './MockInjections.vue';
export { default as MockI18n } from './MockI18n.vue';
export { default as QueryComponent } from './QueryComponent.vue';
export { default as MockTransitions } from './MockTransitions.vue';
export { mockSFC, unmockSFC } from './mockSFC.vue';
export { mockProperty, unmockProperty, setter } from './mock-utils';
export { nextRequestFulfilled } from './moxios-utils';
export { createStub } from './stubs';

export function wait(delay) {
    return new Promise(resolve => {
        setTimeout(resolve, delay);
    });
}
