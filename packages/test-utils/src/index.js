export { default as MockInjections } from './MockInjections';
export { default as MockI18n } from './MockI18n';
export { default as QueryComponent } from './QueryComponent';
export { default as MockTransitions } from './MockTransitions';
export { mockSFC, unmockSFC } from './mockSFC';
export { mockProperty, unmockProperty, setter } from './mock-utils';
export { nextRequestFulfilled } from './moxios-utils';
export { createStub } from './stubs';

export function wait(delay) {
    return new Promise(resolve => {
        setTimeout(resolve, delay);
    });
}
