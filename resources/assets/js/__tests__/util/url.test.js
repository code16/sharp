import { router } from '../../router';
import { 
    formUrl, 
    showUrl, 
    listUrl, 
    routeUrl, 
    getBackUrl, 
    getListBackUrl,
} from 'sharp';
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
            .toEqual('/BASE_URL/form/entityKey/instanceId?x-access-from=ui');
    });
    test('showUrl', () => {
        router(true).addRoutes(showRoutes);
        expect(showUrl({ entityKey:'entityKey', instanceId:'instanceId' }))
            .toEqual('/BASE_URL/show/entityKey/instanceId?x-access-from=ui');
    });
    test('listUrl', () =>{
        router(true).addRoutes(listRoutes);
        expect(listUrl('entityKey'))
            .toEqual('/BASE_URL/list/entityKey?x-access-from=ui');
    });
    test('routeUrl', () => {
        router(true).addRoutes([{ path:'/test', name:'test' }]);
        const url = routeUrl({ name:'test', query: { q:'bbb' } });
        expect(url).toMatch('/BASE_URL/test');
        expect(url).toMatch('q=bbb');
        expect(url).toMatch('x-access-from=ui');
    });
    test('getBackUrl', () => {
        expect(getBackUrl([
            { url:'/list', type: 'entityList' }, 
            { url:'/show', type: 'show' }, 
            { url:'/form', type: 'form' }
        ]))
        .toEqual('/show?x-access-from=ui');
    });
    test('getListBackUrl', () => {
        expect(getListBackUrl([
            { url:'/list', type: 'entityList' }, 
            { url:'/show', type: 'show' }, 
            { url:'/form', type: 'form' }
        ]))
        .toEqual('/list?x-access-from=ui');
    });
})