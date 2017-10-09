import Vue from 'vue';
import FieldsLayout from '../components/form/FieldsLayout.vue';

import * as gridModule from '../components/Grid.vue';

import { MockInjections, mockSFC } from "./utils";

describe('field-container', () => {
    Vue.component('sharp-fields-layout', FieldsLayout);

    beforeEach(()=>{
        mockSFC(gridModule.default,{
            template: `
            <div>
                <slot v-bind="rows[0][0]"></slot>
            </div>
            `
        });

        Vue.component('field-mock', {
            render: ()=>null
        });

        document.body.innerHTML = `
            <div id="app">
                <fields-layout :layout="layout">
                    <template scope="fieldLayout">
                        <field-mock v-bind="fieldLayout" ref="fieldMock"></field-mock>           
                    </template>
                </fields-layout>      
            </div>
        `
    });

    it('can mount fields layout', async () => {
        await createVm({
            propsData: {
                layout: [
                    [{ key:'title' }]
                ]
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('can mount "fieldset" fields layout', async () => {
        await createVm({
            propsData: {
                layout: [
                    [{ legend: 'Fieldset' }]
                ]
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

});

async function createVm(customOptions={}) {

    const vm = new Vue({
        el: '#app',
        mixins: [MockInjections, customOptions],

        props: ['layout']
    });

    await Vue.nextTick();

    return vm.$children[0];
}