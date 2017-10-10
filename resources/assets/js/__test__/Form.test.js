import Vue from 'vue';
import Form from '../components/form/Form.vue';

import * as fieldDisplayModule from '../components/form/field-display/FieldDisplay';
import * as gridModule from '../components/Grid.vue';
import * as fieldsLayoutModule from '../components/form/FieldsLayout.vue';
import * as tabbedLayoutModule from '../components/TabbedLayout.vue';

import { ErrorNode } from '../mixins';

import { mockSFC, unmockSFC } from "./utils";

import moxios from 'moxios';
import {MockInjections, MockI18n} from "./utils";
import { nextRequestFulfilled } from './utils/moxios-utils';

describe('sharp-form', ()=>{
    Vue.use(MockI18n);
    MockI18n.mockLangFunction();

    Vue.component('sharp-form', Form);
    Vue.component('sharp-field-display', {
        name: 'SharpFieldContainer',
        inheritAttrs: false,
        mixins: [ ErrorNode ],
        template: '<div> FIELD DISPLAY MOCK </div>'
    });

    beforeAll(()=>{
        mockSFC(gridModule,{
            template: `
            <div id="MOCKED_GRID">
                <slot v-bind="rows[0][0]"></slot>
            </div>
            `
        });

        mockSFC(fieldsLayoutModule, {
            template: `
            <div id="MOCKED_FIELDS_LAYOUT">
                <slot v-bind="layout[0][0]"></slot>
            </div>
            `
        });

        mockSFC(tabbedLayoutModule, {
            template:`
            <div id="MOCKED_TABBED_LAYOUT">
                <slot v-bind="layout.tabs[0]"></slot>
            </div>
            `
        });
    });

    afterAll(()=>{
        unmockSFC(gridModule);
        unmockSFC(tabbedLayoutModule);
        unmockSFC(fieldsLayoutModule);
    });

    beforeEach(()=>{
        document.body.innerHTML = `
            <div id="app">
                <sharp-form 
                    entity-key="spaceship" 
                    instance-id="10"
                    :independant="independant" 
                    :props="props"
                    :ignore-authorizations="ignoreAuthorizations">
                </sharp-form>
            </div>
        `;
    });

    afterEach(()=>{
        moxios.uninstall();
    });

    it('can mount sharp-form', async ()=>{
        await createVm();

        await nextRequestFulfilled({
            status: 200,
            response: {
                data:{
                    title: null
                },
                fields: {
                    title: {
                        type: 'text'
                    }
                },

                layout: {
                    tabs : [
                        {
                            columns: [
                                {
                                    fields: [
                                        [{ key: 'title'}]
                                    ]
                                }
                            ]
                        }
                    ]
                }
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('can mount "independant" sharp-form', async ()=>{
        await createVm({
            propsData:{
                independant: true,
                props: {
                    data:{
                        title: null
                    },
                    fields: {
                        title: {
                            type: 'text'
                        }
                    },

                    layout: {
                        tabs : [
                            {
                                columns: [
                                    {
                                        fields: [
                                            [{ key: 'title'}]
                                        ]
                                    }
                                ]
                            }
                        ]
                    }
                }
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('can mount sharp-form with alert', async ()=>{
        let $form = await createVm({
            propsData:{
                independant: true,
                props: {
                    data:{
                        title: null
                    },
                    fields: {
                        title: {
                            type: 'text'
                        }
                    },

                    layout: {
                        tabs : [
                            {
                                columns: [
                                    {
                                        fields: [
                                            [{ key: 'title'}]
                                        ]
                                    }
                                ]
                            }
                        ]
                    }
                }
            }
        });

        $form.errors = {
            title: 'error'
        };

        await Vue.nextTick();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

});

async function createVm(customOptions={}) {

    const vm = new Vue({
        el: '#app',
        mixins: [MockInjections, customOptions],

        props: ['independant', 'props', 'ignoreAuthorizations'],

        created() {
            let { axiosInstance } = this._provided;
            moxios.install(axiosInstance);
            moxios.uninstall = moxios.uninstall.bind(moxios, axiosInstance);
        }
    });

    await Vue.nextTick();

    return vm.$children[0];
}