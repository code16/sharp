import VueRouter from "vue-router";
import { createLocalVue } from "@vue/test-utils";
import { router } from '../../router';
import {
    formUrl,
    showUrl,
    listUrl,
    getBackUrl,
    getDeleteBackUrl,
} from 'sharp';
import { routeUrl } from 'sharp/router';
import listRoutes from 'sharp-entity-list/src/routes';
import formRoutes from 'sharp-form/src/routes';
import showRoutes from 'sharp-show/src/routes';

jest.mock('../../consts',()=>({
    BASE_URL: 'BASE_URL'
}));

describe('url', ()=>{
    test('formUrl', () => {
        router(true).addRoutes(formRoutes);
        expect(formUrl({ entityKey:'entityKey', instanceId:'instanceId' }))
            .toEqual('/BASE_URL/s-form/entityKey/instanceId');
    });
    test('showUrl', () => {
        router(true).addRoutes(showRoutes);
        expect(showUrl({ entityKey:'entityKey', instanceId:'instanceId' }))
            .toEqual('/BASE_URL/s-show/entityKey/instanceId');
    });
    test('resolve nested show', () => {
        router(true).addRoutes(showRoutes);
        expect(router().resolve('/s-list/spaceship/s-show/spaceship/2/s-show/pilot/2').route).toMatchObject({
            name: 'show',
            params: {
                entityKey: 'pilot',
                instanceId: '2',
            }
        });
    });
    test('listUrl', () => {
        router(true).addRoutes(listRoutes);
        expect(listUrl('entityKey'))
            .toEqual('/BASE_URL/s-list/entityKey');
    });
    test('routeUrl', () => {
        router(true).addRoutes([{ path:'/test', name:'test' }]);
        const url = routeUrl({ name:'test', query: { q:'bbb' } });
        expect(url).toMatch('/BASE_URL/test');
        expect(url).toMatch('q=bbb');
    });
    test('routeUrl append', () => {
        window.location.pathname = '/BASE_URL/test';
        const $router = router(true)
        const Vue = createLocalVue();
        Vue.use(VueRouter);
        new Vue({ router: $router });
        $router.addRoutes([
            { path:'/test', name: 'test' },
            { path:'/foo', name: 'foo' }
        ]);
        const url = routeUrl({ name:'foo', query: { q:'bbb' } }, { append: true });
        expect(url).toMatch('/BASE_URL/test/foo');
        expect(url).toMatch('q=bbb');
    });
    test('getBackUrl', () => {
        expect(getBackUrl([
            { url:'/s-list', type: 'entityList' },
            { url:'/s-show', type: 'show' },
            { url:'/s-form', type: 'form' }
        ]))
        .toEqual('/s-show');
    });
    test('getDeleteBackUrl', () => {
        router(true);
        router().addRoutes(formRoutes);
        router().addRoutes(showRoutes);
        router().addRoutes(listRoutes);

        expect(getDeleteBackUrl([
            { url:'/s-list', type: 'entityList' },
            { url:'/s-show/spaceship/42', type: 'show' },
            { url:'/s-form/spaceship/42', type: 'form' }
        ]))
        .toEqual('/s-list');

        expect(getDeleteBackUrl([
            { url:'/s-list', type: 'entityList' },
            { url:'/s-show/spaceship/42', type: 'show' },
            { url:'/s-form/spaceship-pilot/42', type: 'form' }
        ]))
        .toEqual('/s-show/spaceship/42');

        expect(getDeleteBackUrl([
            { url:'/s-show/single', type: 'show' },
            { url:'/s-form/spaceship-pilot/42', type: 'form' }
        ]))
        .toEqual('/s-show/single');
    });
})
