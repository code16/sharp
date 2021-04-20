import moxios from "moxios";
import { nextRequestFulfilled, MockI18n } from "@sharp/test-utils";
import { showAlert } from "../util/dialogs";
import { createApi as baseCreateApi } from "../api";

jest.mock('../util/dialogs');

describe('api', () => {

    function createApi() {
        const api = baseCreateApi();
        moxios.install(api);
        return api;
    }

    let defaultDelay = moxios.delay;
    beforeAll(() => moxios.delay = 10);
    afterAll(() => moxios.delay = defaultDelay);

    beforeEach(() => {
        showAlert.mockClear();
        MockI18n.mockLangFunction();
    });

    afterEach(() => {
        moxios.uninstall();
    })

    describe('intercept response [error]', ()=>{

        test('parse blob to json', async ()=>{
            const api = createApi();

            api.get('/', { responseType: 'blob' }).catch(e => {
                //console.log(e) //[debug]
            });

            const response = await nextRequestFulfilled({
                status: 400,
                response: new Blob([JSON.stringify({ errors: {} })], { type: 'application/json' })
            }, 100);

            expect(response.data).toEqual({ errors: {} });
        });

        test('show error modal on 401 and redirect on login page when click OK', async ()=>{
            const api = createApi();

            api.get('/').catch(e => {
                // console.error(e)
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
            const api = createApi();


            api.get('/').catch(e=>{});
            await nextRequestFulfilled({
                status: 403,
                response: {}
            });
            expect(showAlert).toHaveBeenCalledTimes(1);
            expect(showAlert).toHaveBeenLastCalledWith("{{ modals.403.message }}", {
                title: "{{ modals.403.title }}",
                isError: true,
            });

            api.post('/').catch(e=>{});
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

            api.get('/').catch(e=>{});
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

            api.get('/').catch(e=>{});
            await nextRequestFulfilled({
                status: 500,
                response: {}
            });
            expect(showAlert).toHaveBeenCalledTimes(4);
            expect(showAlert).toHaveBeenLastCalledWith("{{ modals.500.message }}", {
                title: "{{ modals.500.title }}",
                isError: true,
            });

            api.get('/').catch(e=>{});
            await nextRequestFulfilled({
                status: 404,
                response: {}
            });
            expect(showAlert).not.toHaveBeenCalledTimes(5);
        });
    });
});
