import Vue from 'vue';
import DynamicViewMixin from '../components/DynamicViewMixin';

import { MockInjections, MockI18n } from "./utils";
import { mockProperty, unmockProperty, setter } from "./utils/mock-utils";

import moxios from 'moxios';
import { nextRequestFulfilled } from "./utils/moxios-utils";


describe('dynamic-view-mixin',()=>{
    Vue.component('sharp-dynamic-view', {
        mixins:[DynamicViewMixin],
        props: {
            apiPath: String,
            apiParams: Object,
            synchronous: Boolean
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
                <sharp-dynamic-view api-path="/test-api/path" :api-params="{ query: 'aaa' }" :synchronous="synchronous"></sharp-dynamic-view>    
            </div>
        `;
        MockI18n.mockLangFunction();
    });

    afterEach(()=>{
        moxios.uninstall();
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

        let showLoadingEmitted = jest.fn();

        $view.mainLoading.$on('show', showLoadingEmitted);

        interceptors.request.forEach(({ fulfilled }) => {
            fulfilled();
        });

        expect(showLoadingEmitted).toHaveBeenCalled();
    });

    test('intercept response [success]: hide main loading', async ()=>{
        let $view = await createVm();

        let { interceptors } = $view.axiosInstance;

        let hideLoadingEmitted = jest.fn();

        $view.mainLoading.$on('hide', hideLoadingEmitted);

        interceptors.response.forEach(({ fulfilled }) => {
            fulfilled();
        });

        expect(hideLoadingEmitted).toHaveBeenCalled();
    });


    describe('intercept response [error]', ()=>{
        let defaultDelay = moxios.delay;
        beforeAll(()=>moxios.delay = 10);
        afterAll(()=>moxios.delay = defaultDelay);

        test('hide loading', async ()=>{
            let $view = await createVm();

            let hideLoadingEmitted = jest.fn();

            $view.mainLoading.$on('hide', hideLoadingEmitted);

            $view.post().catch(e=>{
                // console.log(e) //[debug]
            });

            await nextRequestFulfilled({
                status: 400,
                response: {}
            });

            expect(hideLoadingEmitted).toHaveBeenCalled();
        });

        test('parse blob to json', async ()=>{
            let $view = await createVm();

            $view.axiosInstance.get('/', { responseType: 'blob' }).catch(e=>{
                //console.log(e) //[debug]
            });

            let response = await nextRequestFulfilled({
                status: 400,
                response: new Blob([JSON.stringify({ errors: {} })], { type: 'application/json' })
            });

            expect(response.data).toEqual({ errors: {} });
        });

        test('show error modal on 401 and redirect on login page when click OK', async ()=>{
            let $view = await createVm();

            let showMainMoadlEmitted = jest.fn();

            $view.actionsBus.$on('showMainModal', showMainMoadlEmitted);

            $view.axiosInstance.get('/').catch(e=>{
                //console.log(e) //[debug]
            });

            await nextRequestFulfilled({
                status: 401,
                response: {
                    message: 'unauthorized'
                }
            });

            expect(showMainMoadlEmitted).toHaveBeenCalledTimes(1);
            expect(showMainMoadlEmitted).toHaveBeenCalledWith({
                title: expect.stringMatching(/.+/),
                text: 'unauthorized',
                isError: true,
                okCallback: expect.any(Function)
            });

            let { okCallback } = showMainMoadlEmitted.mock.calls[0][0];

            mockProperty(location,'href');

            okCallback();

            expect(setter(location,'href')).toHaveBeenCalledWith('/sharp/login');

            unmockProperty(location,'href');
        });

        test('show error modal on else server response status', async () => {
            let $view = await createVm();

            let showMainMoadlEmitted = jest.fn();

            $view.actionsBus.$on('showMainModal', showMainMoadlEmitted);

            $view.axiosInstance.get('/').catch(e=>{});
            await nextRequestFulfilled({
                status: 403,
                response: {}
            });
            expect(showMainMoadlEmitted).toHaveBeenCalledTimes(1);
            expect(showMainMoadlEmitted).toHaveBeenLastCalledWith({
                title: expect.stringMatching(/.+/),
                text: expect.stringMatching(/.+/),
                isError: true,
                okCloseOnly: true
            });

            $view.axiosInstance.post('/').catch(e=>{});
            await nextRequestFulfilled({
                status: 404,
                response: {
                    message: 'Not found'
                }
            });
            expect(showMainMoadlEmitted).toHaveBeenCalledTimes(2);
            expect(showMainMoadlEmitted).toHaveBeenLastCalledWith({
                title: expect.stringMatching(/.+/),
                text: 'Not found',
                isError: true,
                okCloseOnly: true
            });

            $view.axiosInstance.get('/').catch(e=>{});
            await nextRequestFulfilled({
                status: 417,
                response: {
                    message: 'custom error'
                }
            });
            expect(showMainMoadlEmitted).toHaveBeenCalledTimes(3);
            expect(showMainMoadlEmitted).toHaveBeenLastCalledWith({
                title: expect.stringMatching(/.+/),
                text: 'custom error',
                isError: true,
                okCloseOnly: true
            });

            $view.axiosInstance.get('/').catch(e=>{});
            await nextRequestFulfilled({
                status: 500,
                response: {}
            });
            expect(showMainMoadlEmitted).toHaveBeenCalledTimes(4);
            expect(showMainMoadlEmitted).toHaveBeenLastCalledWith({
                title: expect.stringMatching(/.+/),
                text: expect.stringMatching(/.+/),
                isError: true,
                okCloseOnly: true
            });

            $view.axiosInstance.get('/').catch(e=>{});
            await nextRequestFulfilled({
                status: 404,
                response: {}
            });
            expect(showMainMoadlEmitted).not.toHaveBeenCalledTimes(5);

        });
    });

    test('show loading on created if asynchronous component', async () => {
        let mainLoadingShowEmitted = jest.fn();
        await createVm({
            created() {
                this._provided.mainLoading.$on('show',mainLoadingShowEmitted);
            }
        });
        expect(mainLoadingShowEmitted).toHaveBeenCalledTimes(1);
    });

    test('should not show loading on created if synchronous component', async () => {
        let mainLoadingShowEmitted = jest.fn();
        await createVm({
            propsData: {
                synchronous: true
            },
            created() {
                this._provided.mainLoading.$on('show',mainLoadingShowEmitted);
            }
        });
        expect(mainLoadingShowEmitted).toHaveBeenCalledTimes(0);
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