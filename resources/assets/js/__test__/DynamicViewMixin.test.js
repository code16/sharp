import Vue from 'vue';
import DynamicViewMixin from '../components/DynamicViewMixin';

import { MockInjections, MockI18n, wait } from "./utils/index";

import moxios from 'moxios';

import { nextRequestFulfilled } from "./utils/moxios-utils";

describe('dynamic-view-mixin',()=>{
    Vue.component('sharp-dynamic-view', {
        mixins:[DynamicViewMixin],
        props: {
            apiPath: String,
            apiParams: Object,
        },
        methods: {
            mount() {}
        },
        created() {
            this.mount = jest.fn();
        },
        render: ()=>null
    });

    beforeEach(()=>{
        document.body.innerHTML = `
            <div id="app">
                <sharp-dynamic-view api-path="/test-api/path" :api-params="{ query: 'aaa' }"></sharp-dynamic-view>    
            </div>
        `;
        MockI18n.mockLangFunction();
    });

    afterEach(()=>{
        moxios.uninstall();
    });

    it('get success', async ()=>{
        let $view = await createVm();

        expect($view.ready).toBe(false);

        let successCallback = jest.fn();

        $view.get().then(successCallback);
        let response = await nextRequestFulfilled({
            status: 200,
            response: {
                layout: {}
            }
        }, 10);

        expect(successCallback).toHaveBeenCalledWith(response);

        let { request } = response;
        expect(request.config).toMatchObject({
            method: 'get',
            url: '/test-api/path'
        });

        expect($view.mount).toHaveBeenCalledTimes(1);
        expect($view.mount).toHaveBeenCalledWith({ layout: {} });

        expect($view.ready).toBe(true);
    });

    it('get error', async ()=>{
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

    it('post success', async ()=>{
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

    it('post custom config', async ()=>{
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

    it('post error', async ()=>{
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
});

async function createVm(customOptions={}) {
    let vm = new Vue({
        el: '#app',
        mixins:[MockInjections, customOptions],

        created() {
            let { axiosInstance } = this._provided;
            moxios.install(axiosInstance);
            moxios.uninstall = moxios.uninstall.bind(moxios, axiosInstance);
        }
    });
    await Vue.nextTick();
    return vm.$children[0];
}