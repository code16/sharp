import { shallowMount } from '@vue/test-utils';

export function createStub(component) {
    const name = component.name || 'anonymous';
    const wrapper = shallowMount({
        render: h => h(name),
        components: { [name]: component }
    }, {
        stubs: { [name]: true },
    });
    return wrapper.vm.$options.components[name];
}