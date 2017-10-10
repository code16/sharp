import Vue from 'vue';
import Form from '../components/form/Form.vue';

import * as fieldDisplayModule from '../components/form/field-display/FieldDisplay';
import * as gridModule from '../components/Grid.vue';
import * as fieldsLayoutModule from '../components/form/FieldsLayout.vue';
import * as tabbedLayoutModule from '../components/TabbedLayout.vue';

import * as consts from '../consts';

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
                    :instance-id="instanceId"
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

    function mockComponent(mockedMethods) {
        return {
            'extends':Form,
            created() {
                mockedMethods.forEach(method=>this[method]=jest.fn());
            }
        }
    }

    const baseLayout = {
        tabs : [
            {
                columns: [
                    {
                        fields: [
                            [{ key:'title' }]
                        ]
                    }
                ]
            }
        ]
    };

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

    it('api path', async ()=> {
        consts.API_PATH = '/test-api';
        let $form = await createVm();

        let { $root:vm } = $form;

        expect($form.apiPath).toBe('/test-api/form/spaceship');

        vm.instanceId = '10';

        await Vue.nextTick();

        expect($form.apiPath).toBe('/test-api/form/spaceship/10');
    });

    it('detect when is creation', async ()=>{
        let $form = await createVm();

        let { $root:vm } = $form;

        expect($form.isCreation).toBe(true);

        vm.instanceId = '10';

        await Vue.nextTick();

        expect($form.isCreation).toBe(false);

    });

    it('is read only', async () => {
        let $form = await createVm({
            propsData:{
                independant: true,
                props: {
                    layout:baseLayout,
                    fields:{},
                    data:{},
                    authorizations: {
                        create: false,
                        update: false
                    }
                }
            },
            // components: {
            //     'sharp-form': mockComponent(['patchLayout'])
            // }
        });

        let { $root:vm } = $form;

        expect($form.isReadOnly).toBe(true);

        Vue.set($form.authorizations, 'create', true);

        expect($form.isReadOnly).toBe(false);

        vm.instanceId = '10';

        await Vue.nextTick();

        expect($form.isReadOnly).toBe(true);

        Vue.set($form.authorizations, 'update', true);

        expect($form.isReadOnly).toBe(false);

        vm.ignoreAuthorizations = true;
        $form.authorizations = { create:false, update:false };

        await Vue.nextTick();

        expect($form.isReadOnly).toBe(false);

    })

});

async function createVm(customOptions={}) {

    const vm = new Vue({
        el: '#app',
        mixins: [MockInjections, customOptions],

        props: ['independant', 'props'],

        'extends': {
            data:()=>({
                instanceId: null,
                ignoreAuthorizations: null
            })
        },

        created() {
            let { axiosInstance } = this._provided;
            moxios.install(axiosInstance);
            moxios.uninstall = moxios.uninstall.bind(moxios, axiosInstance);
        }
    });

    await Vue.nextTick();

    return vm.$children[0];
}