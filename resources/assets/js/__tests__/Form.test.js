import Vue from 'vue';
import Form from '../components/form/Form.vue';
import * as fieldContainerModule from '../components/form/FieldContainer';

import * as gridModule from '../components/Grid.vue';
import * as fieldsLayoutModule from '../components/form/FieldsLayout.vue';
import * as tabbedLayoutModule from '../components/TabbedLayout.vue';


import * as consts from '../consts';


import { mockSFC, unmockSFC, wait } from "./utils";

import { mockProperty, unmockProperty, setter } from "./utils/mock-utils";

import moxios from 'moxios';
import {MockInjections, MockI18n} from "./utils";
import { nextRequestFulfilled } from './utils/moxios-utils';


describe('sharp-form', ()=>{
    Vue.use(MockI18n);
    MockI18n.mockLangFunction();

    Vue.component('sharp-form', Form);
    Vue.component('sharp-field-display', mockSFC(fieldContainerModule));

    const oldDelay = moxios.delay;
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
                <slot v-if="layout[0][0]" v-bind="layout[0][0]"></slot>
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
        moxios.delay = 10;
    });

    afterAll(()=>{
        unmockSFC(gridModule);
        unmockSFC(tabbedLayoutModule);
        unmockSFC(fieldsLayoutModule);
        unmockSFC(fieldContainerModule);
        moxios.delay = oldDelay;
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
        mockProperty(location, 'href');
    });

    afterEach(()=>{
        moxios.uninstall();
        unmockProperty(location, 'href');
    });

    test('can mount sharp-form', async ()=>{
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

    test('can mount "independant" sharp-form', async ()=>{
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

    test('can mount sharp-form with alert', async ()=>{
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
            title: ['required']
        };

        await Vue.nextTick();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    xtest('api path', async ()=> {
        consts.API_PATH = '/test-api';
        let $form = await createVm();

        let { $root:vm } = $form;

        expect($form.apiPath).toBe('/test-api/form/spaceship');

        vm.instanceId = '10';

        await Vue.nextTick();

        expect($form.apiPath).toBe('/test-api/form/spaceship/10');
    });

    test('localized', async ()=>{
        let $form = await createVm();
        expect($form.localized).toBe(false);
        $form.locales = ['fr', 'en'];
        await Vue.nextTick();
        expect($form.localized).toBe(true);
    });

    test('detect when is creation', async ()=>{
        let $form = await createVm();

        let { $root:vm } = $form;

        expect($form.isCreation).toBe(true);

        vm.instanceId = '10';

        await Vue.nextTick();

        expect($form.isCreation).toBe(false);

    });

    test('is read only', async () => {
        let $form = await createVm({
            propsData:{
                independant: true,
                props: {
                    layout:{ tabs:[{ columns:[{ fields:[[]] }] }] },
                    fields:{},
                    data:{},
                    authorizations: {
                        create: false,
                        update: false
                    }
                }
            }
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

    test('is synchronous', async () => {
        let $form = await createVm({
            propsData:{
                independant: true,
                props: {
                    layout:{ tabs:[{ columns:[{ fields:[[]] }] }] },
                    fields:{},
                    data:{},
                }
            }
        });

        expect($form.synchronous).toBe(true);
    });

    test('is asynchronous', async () => {
        let $form = await createVm();

        expect($form.synchronous).toBe(false);
    });


    test('has errors', async () => {
        let $form = await createVm();
        expect($form.hasErrors).toBe(false);

        $form.errors = { field: ['required'] };

        expect($form.hasErrors).toBe(true);

        Vue.set($form.errors.field, 'cleared', true);

        expect($form.hasErrors).toBe(false);
    });

    test('locale selector errors', async ()=> {
        let $form = await createVm();
        $form.locales = ['fr', 'en', 'de'];
        $form.errors = {
            'label': 'error',
            'title.fr': 'error',
        };
        expect($form.localeSelectorErrors).toEqual({ 'fr':true });

        $form.errors = {
            'label': 'error'
        };
        expect($form.localeSelectorErrors).toEqual({ });
    });

    test('expose appropriate props to layout components', async () => {
        let $form = await createVm({
            propsData:{
                independant: true,
                props: {
                    layout:{ tabs:[{ columns:[{ fields:[[{key:'title'}]] }] }] },
                    fields:{ title: { type:'text' } },
                    data:{},
                }
            }
        });

        let { tabbedLayout, columnsGrid, fieldLayout } = $form.$refs;

        expect(tabbedLayout.$options.propsData).toEqual({
            layout: { tabs:[{ columns:[{ fields:[[{key:'title'}]] }] }] } // $form.layout
        });
        expect(columnsGrid.$options.propsData).toEqual({
            rows: [[{ fields:[[{key:'title'}]] }]] // [$form.layout.tabs[0].columns]
        });
        expect(fieldLayout.$options.propsData).toEqual({
            layout: [[{key:'title'}]], // $form.layout.tabs[0].columns[0].fields
            visible: { title: true } // $form.fieldVisible
        });
    });

    test('expose appropriate props to field', async () => {
        let $form = await createVm({
            propsData:{
                independant: true,
                props: {
                    data:{
                        title: null
                    },
                    fields: {
                        title: {
                            key: 'title',
                            type: 'text',
                            localized: true,
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
                    },
                    locales: ['fr', 'en']
                }
            }
        });

        let { field } = $form.$refs;

        expect(field.$options.propsData).toMatchObject({
            fieldKey: 'title',
            fieldLayout: { key: 'title' },
            updateData: $form.updateData,
            locale: 'fr'
        });

        expect(field.$attrs).toMatchObject({
            'error-identifier': 'title',
            'config-identifier': 'title',
            'update-visibility': $form.updateVisibility,
            'context-data': {
                title: null
            },
            'context-fields': {
                title: {
                    type: 'text',
                    readOnly: true
                },
            },
        });

        $form.authorizations = { create: true };

        await Vue.nextTick();

        expect(field.$attrs['context-fields'].readOnly).toBeFalsy();
    });

    test('update data', async () => {
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
        $form.fieldLocalizedValue = jest.fn(()=>'fieldLocalizedValue');
        expect($form.data.title).toBe(null);

        $form.updateData('title', 'text');
        expect($form.fieldLocalizedValue).toHaveBeenCalledWith('title', 'text');
        expect($form.data.title).toBe('fieldLocalizedValue');
    });


    test('update visibility', async () => {
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

        expect($form.fieldVisible.title).toBe(true);

        $form.updateVisibility('title', false);

        expect($form.fieldVisible.title).toBe(false);
    });

    test('mount', async () => {
        let $form = await createVm({
            propsData:{
                independant: true,
                props: {
                    data:{
                        title: null
                    },
                    fields: {
                        title: {
                            key: 'title',
                            type: 'text',
                            localized: true,
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
                    },
                    authorizations: {
                        create: true,
                        update: false
                    },
                    locales: ['en', 'fr']
                }
            }
        });

        expect($form).toMatchObject({
            data:{
                title: null
            },
            fields: {
                title: {
                    type: 'text',
                }
            },
            layout: {
                tabs : [
                    {
                        columns: [
                            {
                                fields: [
                                    [{ key: 'title' }]
                                ]
                            }
                        ]
                    }
                ]
            },
            authorizations: {
                create: true,
                update: false
            },
            locales: ['en', 'fr'],
            fieldLocale: {
                title: 'en'
            }
        });

        $form.patchLayout = ()=>{};
        $form.ready = false;
        $form.mount({ fields:{}, locales:null });

        expect($form.fieldLocale).toEqual({ title:undefined });
    });

    test('mount async', async () => {
        let $form = await createVm();

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
                },
                authorizations: {
                    create: true,
                    update: false
                }
            }
        });

        expect($form).toMatchObject({
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
            },
            authorizations: {
                create: true,
                update: false
            }
        });
    });

    test('handle 422', async () => {
        let $form = await createVm();

        $form.actionsBus.$emit('submit');

        await nextRequestFulfilled({
            status: 422,
            response: {
                errors: {
                    title: ['invalid']
                }
            }
        });

        expect($form.errors).toEqual({
            title: ['invalid']
        });
    });

    test('patch layout', async () => {
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
                                            [{ legend:'fieldset', fields: [[]] }],
                                            [{ legend:'fieldset', fields: [[]] }]
                                        ]
                                    }
                                ]
                            }
                        ]
                    }
                }
            }
        });

        let {
            tabs : [
                {
                    columns: [{ fields }]
                }
            ]
        } = $form.layout;

        expect(typeof fields[0][0].id).toBe('string');
        expect(fields[0][0].id).not.toEqual(fields[1][0].id)
    });


    test('setup action bar correctly', async () => {
        let $form = await createVm();

        let { $root:vm } = $form;

        let setupEmitted = jest.fn();
        $form.actionsBus.$on('setup', setupEmitted);
        $form.setupActionBar = jest.fn($form.setupActionBar);

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
                },
                authorizations: {
                    create: false,
                    update: false
                }
            }
        });

        expect($form.setupActionBar).toHaveBeenCalledTimes(1);
        expect(setupEmitted).toHaveBeenCalledTimes(1);
        expect(setupEmitted.mock.calls[0][0]).toMatchObject({
            showSubmitButton: false,
            showDeleteButton: false,
            showBackButton: true,
            opType: 'create'
        });


        $form.authorizations.create = true;

        $form.setupActionBar();

        expect(setupEmitted).toHaveBeenCalledTimes(2);
        expect(setupEmitted.mock.calls[1][0]).toMatchObject({
            showSubmitButton: true,
            showDeleteButton: false,
            showBackButton: false,
            opType: 'create'
        });


        vm.instanceId = '10';
        $form.authorizations.delete = true;

        await Vue.nextTick();

        $form.setupActionBar();

        expect(setupEmitted).toHaveBeenCalledTimes(3);
        expect(setupEmitted.mock.calls[2][0]).toMatchObject({
            showSubmitButton: false,
            showDeleteButton: true,
            showBackButton: true,
            opType: 'update'
        });

        $form.authorizations.update = true;

        $form.setupActionBar();

        expect(setupEmitted).toHaveBeenCalledTimes(4);
        expect(setupEmitted.mock.calls[3][0]).toMatchObject({
            showSubmitButton: true,
            showDeleteButton: true,
            showBackButton: false,
            opType: 'update'
        });

    });

    test('redirect to list', async () => {
        let $form = await createVm();

        let locationHrefModified = setter(location,'href');

        $form.redirectToList();
        expect(locationHrefModified).toHaveBeenLastCalledWith('/sharp/list/spaceship?restore-context=1');

        $form.redirectToList({ restoreContext: false });
        expect(locationHrefModified).toHaveBeenLastCalledWith('/sharp/list/spaceship');


        $form.redirectToList = jest.fn();
        $form.actionsBus.$emit('submit');

        await nextRequestFulfilled({
            status: 200,
            response: {
                ok: true
            }
        }, 0);

        expect($form.redirectToList).toHaveBeenCalledTimes(1);

        $form.actionsBus.$emit('delete');

        await nextRequestFulfilled({
            status: 200
        }, 0);

        expect($form.redirectToList).toHaveBeenCalledTimes(2);

        $form.actionsBus.$emit('cancel');

        expect($form.redirectToList).toHaveBeenCalledTimes(3);
    });

    test('submit', async () => {
        let $form = await createVm({
            propsData: {
                independant:true,
                props: {
                    data:{},
                    fields: {},
                    layout: {
                        tabs : [
                            {
                                columns: [
                                    {
                                        fields: [[]]
                                    }
                                ]
                            }
                        ]
                    }
                }
            },
        });

        $form.post = jest.fn(async ()=>Promise.resolve({ data: { ok: true } }));

        $form.actionsBus.$emit('submit', { entityKey:'planet' });

        await wait(10);

        expect($form.post).not.toHaveBeenCalled();

        $form.pendingJobs.push('upload');
        $form.actionsBus.$emit('submit');

        await wait(10);

        expect($form.post).not.toHaveBeenCalled();


        let submittedEmmitted = jest.fn();
        $form.$on('submitted', submittedEmmitted);
        $form.pendingJobs = [];
        $form.data = { title: 'My title' };

        $form.actionsBus.$emit('submit', { endpoint:'/test-endpoint', dataFormatter: form => form.data, postConfig:{ responseType:'blob' } });

        await wait(10);

        expect($form.post).toHaveBeenCalledTimes(1);
        expect($form.post).toHaveBeenCalledWith('/test-endpoint', {
            title: 'My title'
        }, {
            responseType:'blob'
        });
        expect(submittedEmmitted).toHaveBeenCalledTimes(1);
        expect(submittedEmmitted).toHaveBeenCalledWith({ data: { ok: true } });
    });

    test('dependant submit', async () => {
        let $form = await createVm();

        $form.post = jest.fn(async ()=>Promise.resolve({ data: { ok: true } }));
        $form.handleError = jest.fn();

        $form.actionsBus.$emit('submit');

        await wait(10);

        expect($form.post).toHaveBeenCalledTimes(1);
        expect($form.post.mock.calls[0][0]).toBeUndefined();
        expect($form.post.mock.calls[0][1]).toBeUndefined();


        expect($form.handleError).not.toHaveBeenCalled();

        $form.post = jest.fn(async ()=>Promise.reject({ error: true }));

        $form.actionsBus.$emit('submit');

        await wait(10);

        expect($form.handleError).toHaveBeenCalledTimes(1);
        expect($form.handleError).toHaveBeenCalledWith({ error: true });
    });

    test('delete', async ()=>{
        // refer 'redirect to list' test
    });

    test('cancel', async ()=>{
        // refer 'redirect to list' test
    });

    test('reset', async ()=>{
        let $form = await createVm();

        $form.data = { field: '...' };
        $form.errors = { field: [] };

        $form.actionsBus.$emit('reset', { entityKey: 'planet' });

        expect($form.data).toEqual({ field: '...' });
        expect($form.errors).toEqual({ field: [] });

        $form.actionsBus.$emit('reset', { entityKey: 'spaceship'});

        expect($form.data).toEqual({});
        expect($form.errors).toEqual({});
    });

    test('pending jobs', async ()=> {
        let $form = await createVm();
        let updateActionsStateEmitted = jest.fn();

        $form.actionsBus.$on('updateActionsState', updateActionsStateEmitted);

        $form.actionsBus.$emit('setPendingJob', { key:'myUpload', origin:'upload' ,value:true });

        expect($form.pendingJobs).toEqual(['myUpload']);
        expect(updateActionsStateEmitted).toHaveBeenLastCalledWith({
            state: 'pending',
            modifier: 'upload'
        });

        $form.actionsBus.$emit('setPendingJob', { key:'myUpload', origin:'upload' ,value:false });

        expect($form.pendingJobs).toEqual([]);
        expect(updateActionsStateEmitted).toHaveBeenLastCalledWith(null);
    });

    test('has localize mixin with right fieldsProps', async () => {
        let $list = await createVm();
        expect($list.$options._localizedForm).toBe('fields');
    });
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