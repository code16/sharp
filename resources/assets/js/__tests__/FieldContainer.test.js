import Vue from 'vue';
import FieldContainer from '../components/form/FieldContainer.vue';
import * as fields from '../components/form/fields';

import { MockInjections } from "./utils/index";
import { ErrorNode, ConfigNode } from '../mixins';

describe('field-container', () => {
    Vue.component('sharp-field-container', FieldContainer);

    beforeEach(() => {
        document.body.innerHTML = `
            <div id="app">
                <sharp-field-container
                    field-key="title"
                    field-type="test-field"
                    :field-props="fieldProps || {}"
                    :locale="locale"
                    label="My beautiful field"
                    help-message="Do not fill this field"
                    error-identifier="title"
                    config-identifier="title"
                >
                </sharp-field-container> 
            </div>
        `;

        fields.default['test-field'] = {
            template: '<div>Template field</div>'
        };
    });

    test('can mount field container', async () => {
        await createVm();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount field container with error', async () => {
        let $fieldContainer = await createVm();

        $fieldContainer.$form.errors = {
            'error.title': [
                'Must be a number'
            ]
        };
        await Vue.nextTick();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount field container with success', async () => {
        let $fieldContainer = await createVm();

        $fieldContainer.state = 'ok';

        await Vue.nextTick();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount field container with extra style', async () => {
        await createVm({
           data: ()=>({
                fieldProps: {
                    extraStyle: 'background:red'
                }
            })
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('set error on field emitted error', async () => {
        let $fieldContainer = await createVm();

        let { field } = $fieldContainer.$refs;
        let tabErrorHandler = jest.fn();

        $fieldContainer.$tab.$on('error', tabErrorHandler);

        field.$emit('error', 'Error message');

        expect($fieldContainer.state).toBe('error');
        expect($fieldContainer.stateMessage).toBe('Error message');
        expect(tabErrorHandler).toHaveBeenCalledWith('error.title');
    });

    test('set success on field emitted ok', async () => {
        let $fieldContainer = await createVm();

        let { field } = $fieldContainer.$refs;

        field.$emit('ok');
        expect($fieldContainer.state).toBe('ok');
        expect($fieldContainer.stateMessage).toBe('');
    });

    test('clear on clear emitted', async () => {
        let $fieldContainer = await createVm();

        let { field } = $fieldContainer.$refs;
        let tabClearHandler = jest.fn(), formClearHandler = jest.fn();

        $fieldContainer.$tab.$on('clear', tabClearHandler);
        $fieldContainer.$form.$on('error-cleared', formClearHandler);
        field.$emit('clear');

        expect($fieldContainer.state).toBe('classic');
        expect($fieldContainer.stateMessage).toBe('');
        expect(tabClearHandler).toHaveBeenCalledWith('error.title');
        expect(formClearHandler).toHaveBeenCalledWith('error.title');
    });

    test('expose proper props', async () => {
        let $fieldContainer = await createVm();

        let { field } = $fieldContainer.$refs;
        expect($fieldContainer.exposedProps).toMatchObject(field.$props);
        expect(field.uniqueIdentifier).toBe('error.title');
        expect(field.fieldConfigIdentifier).toBe('config.title');
    });

    test('responsive to $form.errors object', async () => {
        let $fieldContainer = await createVm();

        $fieldContainer.clear = jest.fn();
        $fieldContainer.setError = jest.fn();

        let { $form } = $fieldContainer;

        $form.errors = {
            'error.title': null
        };
        await Vue.nextTick();
        expect($fieldContainer.clear).toHaveBeenCalled();

        $form.errors = {
            'error.title': ['Error message']
        };
        await Vue.nextTick();
        expect($fieldContainer.setError).toHaveBeenCalledWith('Error message');
    });

    test('update errors on locale changed', async () => {
        let $fieldContainer = await createVm({
            data:()=>({locale:'en'})
        });
        let { $root:vm, $form } = $fieldContainer;

        $fieldContainer.updateError = jest.fn();
        $form.errors = { error1: 'text' };
        vm.locale = 'fr';

        await Vue.nextTick();
        expect($fieldContainer.updateError).toHaveBeenCalledWith({ error1: 'text' });
    });
});

async function createVm(customOptions={}) {

    const vm = new Vue({
        el: '#app',
        mixins: [MockInjections, customOptions, ErrorNode, ConfigNode],

        'extends': {
            data:()=>({
                fieldProps: {},
                locale: null
            }),
            propsData: {
                errorIdentifier: 'error',
                configIdentifier: 'config'
            },
        }
    });

    await Vue.nextTick();

    return vm.$children[0];
}