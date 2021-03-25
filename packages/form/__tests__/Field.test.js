import Vue from 'vue';
import Field from '../src/components/Field.vue';
import * as fields from '../src/components/fields';

import { logError } from 'sharp';

jest.mock('sharp');

describe('sharp-field', () => {
    Vue.component('sharp-field', Field);

    beforeEach(() => {
        document.body.innerHTML = `
            <div id="app">
                <sharp-field field-key="title" 
                :field-type="fieldType" 
                :field-props="fieldProps" 
                :field-layout="fieldLayout" 
                :value="value"
                unique-identifier="list.0.title"
                field-config-identifier="list.title"
                :update-data="updateData">
                </sharp-field>      
            </div>
        `;
    });

    test("can't mount and display error if unknown field", async () => {
        await createVm({
            propsData: {
                fieldType: 'unknown'
            }
        });

        expect(logError).toHaveBeenCalled();
        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount field', async () => {
        fields.default['test-field'] = {
            template: '<div>Template field</div>'
        };

        await createVm({
            propsData: {
                fieldType: 'test-field',
                fieldProps: {}
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('expose proper props', async () => {
        let $field = await createVm({
            propsData: {
                fieldType: 'text',
                fieldProps: {
                    placeholder: 'Titre'
                },
                fieldLayout: {
                    tabs: []
                }
            },
            data:()=>({ value: 'Hello' })
        });

        let { $children:[renderedField] } = $field;

        expect(renderedField.$vnode.data.props).toEqual({
            fieldKey: 'title',
            fieldLayout: {
                tabs: []
            },
            value: 'Hello',
            placeholder: 'Titre',
            uniqueIdentifier: 'list.0.title',
            fieldConfigIdentifier: 'list.title',
            root: false,
        });
    });

    test('call update if input emitted', async () => {
        let updateData = jest.fn();
        let $field = await createVm({
            propsData: {
                fieldType: 'text',
                fieldProps: {}
            },
            methods: {
                updateData
            }
        });

        let { $children:[renderedField] } = $field;

        renderedField.$emit('input', 'Coucou');

        expect(updateData).toHaveBeenCalledTimes(1);
        expect(updateData).toHaveBeenCalledWith('title','Coucou', { forced:undefined });

        updateData.mockClear();
        renderedField.$emit('input', 'Bonjour', { force: true });

        expect(updateData).toHaveBeenCalledTimes(1);
        expect(updateData).toHaveBeenCalledWith('title', 'Bonjour', { forced: true });
    });

    test("don't call update when readOnly if input emitted", async () => {
        let updateData = jest.fn();
        let $field = await createVm({
            propsData: {
                fieldType: 'text',
                fieldProps: {
                    readOnly: true
                }
            },
            methods: {
                updateData
            }
        });

        let { $children:[renderedField] } = $field;

        renderedField.$emit('input', 'Coucou');

        expect(updateData).not.toHaveBeenCalled();

        renderedField.$emit('input', 'Coucou', { force: true });

        expect(updateData).toHaveBeenCalled();

    });

    test('provide "$field" injection', async () => {
        fields.default['test-field'] = {
            template: '<div>Template field</div>',
            inject:['$field']
        };
        let $field = await createVm({
            propsData: {
                fieldType: 'test-field',
                fieldProps: {}
            },
        });

        let { $children:[renderedField] } = $field;

        expect(renderedField.$field).toBe($field);
    });
});

async function createVm(customOptions={}) {

    const vm = new Vue({
        el: '#app',
        mixins: [customOptions],

        props:['fieldType', 'fieldProps', 'fieldLayout'],

        'extends': {
            methods: {
                updateData:()=>{}
            },
            data:()=>({
                value: null
            })
        }
    });

    await Vue.nextTick();

    return vm.$children[0];
}
