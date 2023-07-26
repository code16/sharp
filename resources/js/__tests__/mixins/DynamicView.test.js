import Vue from 'vue';
import DynamicView from '../../mixins/DynamicView';
import { showAlert } from "../../util/dialogs";
import { handleNotifications } from "../../util/notifications";
import {
    MockInjections,
    MockI18n,
    nextRequestFulfilled
} from "@sharp/test-utils";

import moxios from 'moxios';

jest.mock('../../util/dialogs');
jest.mock('../../util/notifications');

describe('dynamic-view',()=>{
    Vue.component('sharp-dynamic-view', {
        mixins: [DynamicView],
        props: {
            apiPath: String,
            apiParams: Object,
            synchronous: Boolean
        },
        methods: {
            mount() {}
        },
        beforeCreate() {
            this.$store = {
                dispatch: jest.fn(),
            }
        },
        created() {
            this.mount = jest.fn();
        },
        render: ()=>null
    });

    beforeEach(()=>{
        document.body.innerHTML = `
            <div id="app">
                <sharp-dynamic-view api-path="/test-api/path" :api-params="{ query: 'aaa' }" :synchronous="synchronous"></sharp-dynamic-view>    
            </div>
        `;
        MockI18n.mockLangFunction();
    });

    afterEach(()=>{
        moxios.uninstall();
        showAlert.mockClear();
    });

    test('get success', async ()=>{
        const $view = await createVm();
        const successCallback = jest.fn();

        $view.get().then(successCallback);

        const data = {
            layout: {},
            notifications: [{ title:'title' }]
        };
        const response = await nextRequestFulfilled({
            status: 200,
            response: data,
        }, 10);

        expect(successCallback).toHaveBeenCalledWith(response);

        let { request } = response;
        expect(request.config).toMatchObject({
            method: 'get',
            url: '/test-api/path'
        });

        expect($view.mount).toHaveBeenCalledTimes(1);
        expect($view.mount).toHaveBeenCalledWith(data);

        expect(handleNotifications).toHaveBeenCalledWith([{ title:'title' }]);
    });

    test('get error', async ()=>{
        let $view = await createVm();

        let errorCallback = jest.fn();
        $view.get().catch(errorCallback);

        let response = await nextRequestFulfilled({
            status: 404,
            response: {
                errors: {}
            }
        }, 10);

        expect(errorCallback).toHaveBeenCalled();
        //console.dir(errorCallback.mock.calls[0][0]);
        expect(errorCallback.mock.calls[0][0].response).toEqual(response);
    });

    test('post success', async ()=>{
        let $view = await createVm();

        let successCallback = jest.fn();
        $view.data = {
            fields: {}
        };
        $view.post().then(successCallback);
        let response = await nextRequestFulfilled({
            status: 200,
            response: {
                ok: true
            }
        }, 10);

        expect(successCallback).toHaveBeenCalledWith(response);

        let { request } = response;
        expect(request.config).toMatchObject({
            method: 'post',
            url: '/test-api/path',
            data: JSON.stringify({ fields:{} })
        });
    });

    test('post custom config', async ()=>{
        let $view = await createVm();

        $view.post('/test-api/argument/path', { customData: {} });
        let response = await nextRequestFulfilled({
            status: 200,
            response: {
                ok: true
            }
        }, 10);

        let { request } = response;
        expect(request.config).toMatchObject({
            url: '/test-api/argument/path',
            data: JSON.stringify({ customData: {} })
        });
    });

    test('post error', async ()=>{
        let $view = await createVm();

        let errorCallback = jest.fn();
        $view.post().catch(errorCallback);

        let response = await nextRequestFulfilled({
            status: 404,
            response: {
                errors: {}
            }
        }, 10);

        expect(errorCallback).toHaveBeenCalled();
        expect(errorCallback.mock.calls[0][0].response).toEqual(response);
    });

    test('intercept request : show main loading', async ()=>{
        let $view = await createVm();

        let { interceptors } = $view.axiosInstance;

        interceptors.request.forEach(({ fulfilled }) => {
            fulfilled();
        });

        expect($view.$store.dispatch).toHaveBeenCalledWith('setLoading', true);
    });

    test('intercept response [success]: hide main loading', async ()=>{
        let $view = await createVm();

        let { interceptors } = $view.axiosInstance;

        interceptors.response.forEach(({ fulfilled }) => {
            fulfilled();
        });

        expect($view.$store.dispatch).toHaveBeenCalledWith('setLoading', false);
    });

    test('intercept response [error]: hide loading', async ()=>{
        let $view = await createVm();

        $view.post().catch(e=>{
            // console.log(e) //[debug]
        });

        await nextRequestFulfilled({
            status: 400,
            response: {}
        });

        expect($view.$store.dispatch).toHaveBeenCalledWith('setLoading', false);
    });


    test('show loading on created if asynchronous component', async () => {
        const $view = await createVm();
        expect($view.$store.dispatch).toHaveBeenCalledWith('setLoading', true);
    });

    test('should not show loading on created if synchronous component', async () => {
        const $view = await createVm({
            propsData: {
                synchronous: true
            },
        });
        expect($view.$store.dispatch).not.toHaveBeenCalled();
    });
});

async function createVm(customOptions={}) {
    let vm = new Vue({
        el: '#app',
        mixins:[MockInjections, customOptions],

        props:['synchronous'],

        created() {
            let { axiosInstance } = this._provided;
            moxios.install(axiosInstance);
            moxios.uninstall = moxios.uninstall.bind(moxios, axiosInstance);
        }
    });
    await Vue.nextTick();
    return vm.$children[0];
}
