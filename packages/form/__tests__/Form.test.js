import Vue from 'vue';
import Vuex from 'vuex';
import axios from 'axios';
import Form from '../src/components/Form.vue';
import store from 'sharp/store';

import {MockI18n, nextRequestFulfilled, wait} from "@sharp/test-utils";
import moxios from 'moxios';
import {createLocalVue, shallowMount} from '@vue/test-utils';
import {lang} from "sharp";

jest.mock('sharp');

describe('sharp-form', ()=>{
    Vue.use(MockI18n);

    lang.mockImplementation(key => key);

    function createWrapper({ propsData } = {}) {
        const localVue = createLocalVue();
        localVue.use(Vuex);

        const axiosInstance = axios.create();

        moxios.install(axiosInstance);

        return shallowMount(Form, {
            propsData: {
                entityKey: 'spaceship',
                instanceId: null,
                ignoreAuthorizations: null,
                ...propsData,
            },
            provide: {
                axiosInstance,
            },
            store: new Vuex.Store(store),
            // language=Vue
            stubs: {
                Grid:
                    `<div id="MOCKED_GRID">
                        <slot :item-layout="rows[0][0]" />
                    </div>`,
                FieldsLayout:
                    `<div id="MOCKED_FIELDS_LAYOUT"> 
                        <slot v-if="layout[0][0]" :field-layout="layout[0][0]" />
                    </div>`,
                FieldDisplay: true,
                TabbedLayout:
                    `<div id="MOCKED_TABBED_LAYOUT">
                        <slot :tab="layout.tabs[0]" />
                    </div>`,
            },
            localVue
        });
    }

    function createLayout(fields) {
        return {
            tabs : [
                {
                    columns: [
                        {
                            fields,
                        }
                    ]
                }
            ]
        }
    }

    function createForm({ key='title', type='text', ...props } = {}) {
        return {
            data: { [key]: null },
            fields: {
                [key]: {
                    key,
                    type,
                    ...props,
                }
            },
            layout: createLayout([[{ key }]]),
        }
    }

    function handleSubmitError(e) {
        if(!e.response) {
            throw e;
        }
    }


    const oldDelay = moxios.delay;
    moxios.delay = 10;

    afterAll(() => {
        moxios.delay = oldDelay;
    });

    test('can mount sharp-form', async ()=>{
        const wrapper = createWrapper();

        await nextRequestFulfilled({
            status: 200,
            response: createForm(),
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    test('can mount "independant" sharp-form', ()=>{
        const wrapper = createWrapper({
            propsData:{
                independant: true,
                form: createForm()
            }
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    test('can mount sharp-form with alert', ()=>{
        const wrapper = createWrapper({
            propsData:{
                independant: true,
                form: createForm()
            }
        });

        wrapper.setData({
            errors: {
                title: ['required']
            }
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    test('localized', ()=>{
        const wrapper = createWrapper();
        expect(wrapper.vm.localized).toBe(false);
        wrapper.setData({
            locales: ['fr', 'en']
        });
        expect(wrapper.vm.localized).toBe(true);
    });

    test('detect when is creation', ()=>{
        const wrapper = createWrapper();

        expect(wrapper.vm.isCreation).toBe(true);

        wrapper.setProps({
            instanceId: '10',
        });

        expect(wrapper.vm.isCreation).toBe(false);

    });

    test('is read only', () => {
        const wrapper = createWrapper({
            propsData:{
                independant: true,
                form: {
                    ...createForm(),
                    authorizations: {
                        create: false,
                        update: false
                    }
                }
            }
        });
        expect(wrapper.vm.isReadOnly).toBe(true);

        wrapper.setData({
            authorizations: {
                create: true,
                update: false,
            }
        });
        expect(wrapper.vm.isReadOnly).toBe(false);

        wrapper.setProps({
            instanceId: '10',
        });
        expect(wrapper.vm.isReadOnly).toBe(true);

        wrapper.setData({
            authorizations: {
                create: true,
                update: true,
            }
        });
        expect(wrapper.vm.isReadOnly).toBe(false);

        wrapper.setProps({
            ignoreAuthorizations: true,
        });
        wrapper.setData({
            authorizations: {
                create: false,
                update: false,
            }
        });
        expect(wrapper.vm.isReadOnly).toBe(false);
    })

    test('synchronous', () => {
        const wrapper = createWrapper({
            propsData:{
                independant: true,
                form: createForm(),
            }
        });

        expect(wrapper.vm.synchronous).toBe(true);

        wrapper.setProps({ independant: false });

        expect(wrapper.vm.synchronous).toBe(false);
    });


    test('has errors', () => {
        const wrapper = createWrapper();
        expect(wrapper.vm.hasErrors).toBe(false);

        wrapper.setData({
            errors: {
                field: ['required']
            }
        });

        expect(wrapper.vm.hasErrors).toBe(true);

        wrapper.setData({
            errors: {
                field: null,
            }
        });

        expect(wrapper.vm.hasErrors).toBe(false);
    });

    test('expose appropriate props to layout components', () => {
        const wrapper = createWrapper({
            propsData: {
                independant: true,
                form: createForm()
            }
        });

        let { tabbedLayout, columnsGrid, fieldLayout } = wrapper.vm.$refs;

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
        const wrapper = createWrapper({
            propsData:{
                independant: true,
                form: {
                    ...createForm({ localized: true }),
                    locales: ['fr', 'en']
                }
            }
        });
        await wrapper.vm.$nextTick();
        const field = wrapper.find({ ref:'field' }).vm;

        expect(field.$attrs).toMatchObject({
            'field-key': 'title',
            'field-layout': { key: 'title' },
            'update-data': wrapper.vm.updateData,
            'locale': 'fr',
            'error-identifier': 'title',
            'config-identifier': 'title',
            'update-visibility': wrapper.vm.updateVisibility,
            'context-data': {
                title: null
            },
            'context-fields': {
                title: {
                    type: 'text',
                },
            },
        });
    });

    test('update data', () => {
        const wrapper = createWrapper({
            propsData:{
                independant: true,
                form: createForm(),
            }
        });
        wrapper.vm.fieldLocalizedValue = jest.fn(()=>'fieldLocalizedValue');
        expect(wrapper.vm.data.title).toBe(null);

        wrapper.vm.updateData('title', 'text');
        expect(wrapper.vm.fieldLocalizedValue).toHaveBeenCalledWith('title', 'text');
        expect(wrapper.vm.data.title).toBe('fieldLocalizedValue');
    });


    test('update visibility', async () => {
        const wrapper = createWrapper({
            propsData:{
                independant: true,
                form: createForm()
            }
        });

        expect(wrapper.vm.fieldVisible.title).toBe(true);

        wrapper.vm.updateVisibility('title', false);

        expect(wrapper.vm.fieldVisible.title).toBe(false);
    });

    test('mount', () => {
        const wrapper = createWrapper({
            propsData:{
                independant: true,
                form: {
                    ...createForm({ localized:true }),
                    authorizations: {
                        create: true,
                        update: false
                    },
                    locales: ['en', 'fr']
                }
            }
        });

        expect(wrapper.vm).toMatchObject({
            ...createForm({ localized:true }),
            authorizations: {
                create: true,
                update: false
            },
            locales: ['en', 'fr'],
            fieldLocale: {
                title: 'en'
            }
        });

        wrapper.vm.patchLayout = ()=>{};
        wrapper.vm.ready = false;
        wrapper.vm.mount({ fields:{}, locales:null });

        expect(wrapper.vm.fieldLocale).toEqual({ title:undefined });
    });

    test('mount async', async () => {
        const wrapper = createWrapper();

        await nextRequestFulfilled({
            status: 200,
            response: {
                ...createForm(),
                authorizations: {
                    create: true,
                    update: false
                }
            }
        });

        expect(wrapper.vm).toMatchObject({
            ...createForm(),
            authorizations: {
                create: true,
                update: false
            }
        });
    });

    test('handle 422', async () => {
        const wrapper = createWrapper();

        wrapper.vm.submit().catch(handleSubmitError);

        await nextRequestFulfilled({
            status: 422,
            response: {
                errors: {
                    title: ['invalid']
                }
            }
        });

        expect(wrapper.vm.errors).toEqual({
            title: ['invalid']
        });
    });

    test('patch layout', async () => {
        const wrapper = createWrapper({
            propsData:{
                independant: true,
                form: {
                    ...createForm(),
                    layout: createLayout( [
                        [{ legend:'fieldset', fields: [[]] }],
                        [{ legend:'fieldset', fields: [[]] }]
                    ]),
                }
            }
        });

        let {
            tabs : [
                {
                    columns: [{ fields }]
                }
            ]
        } = wrapper.vm.layout;

        expect(typeof fields[0][0].id).toBe('string');
        expect(fields[0][0].id).not.toEqual(fields[1][0].id)
    });

    test('serialize', () => {
        const wrapper = createWrapper();

        expect(wrapper.vm.serialize()).toEqual({});

        wrapper.setData({
            fields: {
                html: { type:'html' }
            },
            data: {
                field1: 'value',
                html: '<b></b>'
            }
        })

        expect(wrapper.vm.serialize()).toEqual({
            field1: 'value',
        });
    });

    test('setup action bar correctly', async () => {
        const wrapper = createWrapper();

        await nextRequestFulfilled({
            status: 200,
            response: {
                ...createForm(),
                authorizations: {
                    create: false,
                    update: false,
                    delete: false,
                },
                breadcrumb: {
                    visible: true,
                    items: [{ url:'/list' }]
                }
            }
        });

        expect(wrapper.vm.actionBarProps).toMatchObject({
            showSubmitButton: false,
            showDeleteButton: false,
            showBackButton: true,
            create: true,
            breadcrumb: [{ url:'/list' }],
            showBreadcrumb: true,
        });


        wrapper.vm.authorizations.create = true;

        expect(wrapper.vm.actionBarProps).toMatchObject({
            showSubmitButton: true,
            showDeleteButton: false,
            showBackButton: false,
            create: true,
        });


        wrapper.setProps({
            instanceId: '10',
        });
        wrapper.vm.authorizations.delete = true;

        expect(wrapper.vm.actionBarProps).toMatchObject({
            showSubmitButton: false,
            showDeleteButton: true,
            showBackButton: true,
            create: false,
        });

        wrapper.vm.authorizations.update = true;

        expect(wrapper.vm.actionBarProps).toMatchObject({
            showSubmitButton: true,
            showDeleteButton: true,
            showBackButton: false,
            create: false,
        });
    });

    test('redirect on delete', async () => {
        const wrapper = createWrapper();

        jest.spyOn(window.location, 'replace')

        wrapper.vm.handleDeleteClicked();

        await nextRequestFulfilled({
            status: 200,
            response: {
                redirectUrl: '/test'
            }
        });

        expect(window.location.replace).toHaveBeenCalledWith('/test');
    });

    test('submit', async () => {
        const wrapper = createWrapper({
            propsData: {
                independant: true,
                form: createForm(),
            },
        });

        wrapper.vm.post = jest.fn(()=>Promise.resolve({ data: { ok: true } }));

        wrapper.vm.uploadingFields = { field:true };
        wrapper.vm.submit();

        await wait(10);

        expect(wrapper.vm.post).not.toHaveBeenCalled();

        wrapper.vm.uploadingFields = {}
        wrapper.vm.submit();

        await wait(10);

        expect(wrapper.vm.post).toHaveBeenCalledTimes(1);
    });

    test('dependant submit', async () => {
        const wrapper = createWrapper();

        await nextRequestFulfilled({
            status: 200,
            response: {
                ...createForm(),
                breadcrumb: {
                    items: [{ type:'form', name:"Form" }]
                },
            }
        });

        wrapper.vm.post = jest.fn(()=>Promise.resolve({ data: { redirectUrl: '/test' } }));
        jest.spyOn(wrapper.vm, 'handleError');

        wrapper.vm.submit().catch(handleSubmitError);

        await wait(10);

        expect(wrapper.vm.post).toHaveBeenCalledTimes(1);
        expect(wrapper.vm.post.mock.calls[0][0]).toEqual('form/spaceship');
        expect(wrapper.vm.post.mock.calls[0][1]).toEqual({ title: null });

        expect(location.href).toEqual('/test');


        expect(wrapper.vm.handleError).not.toHaveBeenCalled();

        wrapper.vm.post = jest.fn(()=>Promise.reject({ response: { status:500 } }));

        wrapper.vm.submit().catch(handleSubmitError);

        await wait(10);

        expect(wrapper.vm.handleError).toHaveBeenCalledTimes(1);
        expect(wrapper.vm.handleError).toHaveBeenCalledWith({ response: { status:500 } });
    });

    test('delete', async ()=>{
        // refer 'redirect to list' test
    });

    test('cancel', async ()=>{
        // refer 'redirect to list' test
    });

    test('has localize mixin with right fieldsProps', async () => {
        const wrapper = await createWrapper();
        expect(wrapper.vm.$options._localizedForm).toBe('fields');
    });
});
