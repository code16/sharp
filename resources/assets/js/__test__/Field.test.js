import Vue from 'vue';
import Field from '../components/form/Field.vue';
import * as fields from '../components/form/fields';

import * as util from '../util';

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

    it("can't mount and display error if unknown field", async () => {
        util.error = jest.fn((...args)=>console.log(...args));
        await createVm({
            propsData: {
                fieldType: 'unknown'
            }
        });

        expect(util.error).toHaveBeenCalled();
        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('can mount field', async () => {
        fields.NameAssociation['test-field'] = {
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

    it('expose proper props', async () => {
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
            fieldConfigIdentifier: 'list.title'
        });
    });

    it('call update if input emitted', async () => {
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
        expect(updateData).toHaveBeenCalledWith('title','Coucou');
    });

    it("don't call update when readOnly if input emitted", async () => {
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

    it('provide "$field" injection', async () => {
        fields.NameAssociation['test-field'] = {
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