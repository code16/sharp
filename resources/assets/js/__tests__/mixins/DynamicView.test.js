import Vue from 'vue';
import DynamicView from '../../mixins/DynamicView';
import { showAlert } from "../../util/dialogs";
import {
    MockInjections,
    MockI18n,
    nextRequestFulfilled
} from "@sharp/test-utils";

import moxios from 'moxios';

jest.mock('../../util/dialogs');

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
        let $view = await createVm();

        $view.handleNotifications = jest.fn();

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

        expect($view.handleNotifications).toHaveBeenCalledWith({ layout: {} });
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


    describe('intercept response [error]', ()=>{
        let defaultDelay = moxios.delay;
        beforeAll(()=>moxios.delay = 10);
        afterAll(()=>moxios.delay = defaultDelay);

        test('hide loading', async ()=>{
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

        test('parse blob to json', async ()=>{
            let $view = await createVm();

            $view.axiosInstance.get('/', { responseType: 'blob' }).catch(e=>{
                //console.log(e) //[debug]
            });

            let response = await nextRequestFulfilled({
                status: 400,
                response: new Blob([JSON.stringify({ errors: {} })], { type: 'application/json' })
            }, 100);

            expect(response.data).toEqual({ errors: {} });
        });

        test('show error modal on 401 and redirect on login page when click OK', async ()=>{
            let $view = await createVm();

            $view.axiosInstance.get('/').catch(e=>{
                //console.log(e) //[debug]
            });

            await nextRequestFulfilled({
                status: 401,
                response: {
                    message: 'unauthorized'
                }
            });

            expect(showAlert).toHaveBeenCalledTimes(1);
            expect(showAlert).toHaveBeenCalledWith('unauthorized', {
                title: expect.stringMatching(/.+/),
                isError: true,
                okCallback: expect.any(Function)
            });

            let { okCallback } = showAlert.mock.calls[0][1];

            location.reload = jest.fn();

            okCallback();

            expect(location.reload).toHaveBeenCalled();

        });

        test('show error modal on else server response status', async () => {
            let $view = await createVm();


            $view.axiosInstance.get('/').catch(e=>{});
            await nextRequestFulfilled({
                status: 403,
                response: {}
            });
            expect(showAlert).toHaveBeenCalledTimes(1);
            expect(showAlert).toHaveBeenLastCalledWith("{{ modals.403.message }}", {
                title: "{{ modals.403.title }}",
                isError: true,
            });

            $view.axiosInstance.post('/').catch(e=>{});
            await nextRequestFulfilled({
                status: 404,
                response: {
                    message: 'Not found'
                }
            });
            expect(showAlert).toHaveBeenCalledTimes(2);
            expect(showAlert).toHaveBeenLastCalledWith('Not found', {
                title: "{{ modals.404.title }}",
                isError: true,
            });

            $view.axiosInstance.get('/').catch(e=>{});
            await nextRequestFulfilled({
                status: 417,
                response: {
                    message: 'custom error'
                }
            });
            expect(showAlert).toHaveBeenCalledTimes(3);
            expect(showAlert).toHaveBeenLastCalledWith('custom error', {
                title: "{{ modals.417.title }}",
                isError: true,
            });

            $view.axiosInstance.get('/').catch(e=>{});
            await nextRequestFulfilled({
                status: 500,
                response: {}
            });
            expect(showAlert).toHaveBeenCalledTimes(4);
            expect(showAlert).toHaveBeenLastCalledWith("{{ modals.500.message }}", {
                title: "{{ modals.500.title }}",
                isError: true,
            });

            $view.axiosInstance.get('/').catch(e=>{});
            await nextRequestFulfilled({
                status: 404,
                response: {}
            });
            expect(showAlert).not.toHaveBeenCalledTimes(5);

        });
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

    test('notifications', async () => {
        let $view = await createVm();
        $view.$notify = jest.fn();
        jest.spyOn($view, 'showNotification');

        jest.useFakeTimers();

        $view.handleNotifications({ });
        jest.runAllTimers();

        $view.handleNotifications({ notifications:[] });
        jest.runAllTimers();

        let alert1 = { title:'title', message:'message', level:'danger', autoHide: true },
            alert2 = { title:'alert2' };

        $view.handleNotifications({ notifications:[alert1, alert2] });
        jest.runAllTimers();

        expect($view.showNotification).toHaveBeenCalledWith(alert1, expect.anything(), expect.anything());
        expect($view.showNotification).toHaveBeenCalledWith(alert2, expect.anything(), expect.anything());

        expect($view.$notify).toHaveBeenCalledWith(expect.objectContaining({ title:'title', text:'message', type:'danger', duration:4000 }));
        expect($view.$notify).toHaveBeenCalledWith(expect.objectContaining({ duration:-1 }));

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