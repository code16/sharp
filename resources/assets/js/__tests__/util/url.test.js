import Vuex from 'vuex';
import VueRouter from 'vue-router';
import SharpEntityList from 'sharp-entity-list';
import SharpForm from 'sharp-form';
// import SharpShow from 'sharp-show';
import { createLocalVue } from '@vue/test-utils';
import { router as getRouter } from '../../router';
import { formUrl, showUrl } from 'sharp';

jest.mock('../../consts',()=>({
    BASE_URL: 'BASE_URL'
}));

describe('url', ()=>{
    function init() {
        const Vue = createLocalVue();
        Vue.use(Vuex);
        Vue.use(VueRouter);
        return {
            Vue,
            router: getRouter(),
            store: new Vuex.Store()
        }
    }
    // test('formUrl', () => {
    //     const { Vue, router, store } = init();
    //     Vue.use(SharpForm, { router, store });
    //     expect(formUrl({ entityKey:'entityKey', instanceId:'instanceId' })).toEqual('/BASE_URL/form/entityKey/instanceId?x-access-from=ui');
    // });
    // test('showUrl', () => {
    //     const { Vue, router, store } = init();
    //     Vue.use(SharpShow, { router, store });
    //     expect(showUrl({ entityKey:'entityKey', instanceId:'instanceId' })).toEqual('/BASE_URL/show/entityKey/instanceId?x-access-from=ui');
    // })
})