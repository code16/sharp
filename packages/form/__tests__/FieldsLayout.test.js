import Vue from 'vue';
import { Grid as gridModule } from 'sharp/components';
import FieldsLayout from '../src/components/form/ui/FieldsLayout.vue';

import { MockInjections, mockSFC, unmockSFC } from "sharp-test-utils";

describe('fields-layout', () => {
    Vue.component('sharp-fields-layout', FieldsLayout);

    beforeAll(()=>{
        mockSFC(gridModule,{
            template: `
            <div id="MOCKED_GRID">
                <slot v-bind="rows[0][0]"></slot>
            </div>
            `
        });
    });

    afterAll(()=>{
        unmockSFC(gridModule);
    });

    beforeEach(()=>{
        Vue.component('field-mock', {
            render() { return this._v('FIELD MOCK'); }
        });

        document.body.innerHTML = `
            <div id="app">
                <sharp-fields-layout :layout="layout" :visible="visible">
                    <template slot-scope="fieldLayout">
                        <field-mock :data="fieldLayout" ref="fieldMock"></field-mock>           
                    </template>
                </sharp-fields-layout>      
            </div>
        `
    });

    test('can mount fields layout', async () => {
        await createVm({
            propsData: {
                layout: [
                    [{ key:'title' }]
                ]
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "fieldset" fields layout', async () => {
        await createVm({
            propsData: {
                layout: [
                    [{
                        legend: 'Fieldset', fields: [[
                            { key: 'title' }
                        ]]
                    }]
                ],
                visible: {
                    title: true
                }
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "hidden fieldset" fields layout', async () => {
        await createVm({
            propsData: {
                layout: [
                    [{
                        legend: 'Fieldset', fields: [
                            [{ key: 'title' }]
                        ]
                    }]
                ],
                visible: {
                    title: false
                }
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('expose correct props', async () => {
        let { $root:vm } = await createVm({
            propsData: {
                layout: [
                    [{ key:'list' }]
                ]
            }
        });

        expect(vm.$refs.fieldMock.$attrs).toEqual({
            data: { key: 'list' }
        });
    });

    test('expose correct fieldset props', async () => {
        let { $root:vm } = await createVm({
            propsData: {
                layout: [
                    [{
                        legend: 'Fieldset', fields: [
                            [{ key: 'title' }]
                        ]
                    }]
                ],
            }
        });

        expect(vm.$refs.fieldMock.$attrs).toEqual({
            data: { key: 'title' }
        });
    });

    test('fieldset visible', async () => {
        let $fieldsLayout = await createVm({
            propsData: {
                layout: [
                    [{
                        legend: 'Fieldset 1',
                        id: 'fieldset_1',
                        fields: [
                            [{ key: 'title' }, { key: 'subtitle' }],[{ key: 'name' }]
                        ]
                    }]
                ],
                visible: {
                    title: true, subtitle: true, name: false,
                }
            }
        });

        expect($fieldsLayout.fieldsetMap).toEqual({
            'fieldset_1':[{ key: 'title' }, { key: 'subtitle' },{ key: 'name' }]
        });

        expect($fieldsLayout.isFieldsetVisible({ id: 'fieldset_1'})).toBe(true);
    });

    test('fieldset invisible', async () => {
        let $fieldsLayout = await createVm({
            propsData: {
                layout: [
                    [{
                        legend: 'Fieldset 1',
                        id: 'fieldset_1',
                        fields: [
                            [{ key: 'title' }, { key: 'subtitle' }],[{ key: 'name' }]
                        ]
                    }]
                ],
                visible: {
                    title: false, subtitle: false, name: false,
                }
            }
        });

        expect($fieldsLayout.fieldsetMap).toEqual({
            'fieldset_1':[{ key: 'title' }, { key: 'subtitle' },{ key: 'name' }]
        });

        expect($fieldsLayout.isFieldsetVisible({ id: 'fieldset_1'})).toBe(false);
    })

});

async function createVm(customOptions={}) {

    const vm = new Vue({
        el: '#app',
        mixins: [MockInjections, customOptions],

        props: ['layout', 'visible']
    });

    await Vue.nextTick();

    return vm.$children[0];
}