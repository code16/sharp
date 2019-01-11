import Vue from 'vue';
import List from '../components/form/fields/list/List.vue';
import FieldDisplay from '../components/form/field-display/FieldDisplay';

import { MockInjections, MockTransitions, MockI18n, QueryComponent } from './utils';
import { ErrorNode } from '../mixins';

import { mount } from '@vue/test-utils';

describe('list-field', () => {
    Vue.use(MockTransitions);
    Vue.use(MockI18n);
    Vue.use(QueryComponent);

   const fieldDisplayMock = Vue.component('sharp-field-display',{
       name: 'SharpFieldContainer',
       inheritAttrs: false,
       mixins: [ ErrorNode ],
       render:h=>h('div',' FIELD DISPLAY MOCK ')
   });

    beforeEach(()=>{
        document.body.innerHTML = `    
            <div id="app">
                <sharp-list 
                    :value="value" 
                    :field-layout="{ 
                        item:[
                            [ {key:'name'} ]
                        ]
                    }"
                    :item-fields="itemFields || { name: { type:'text' } }"
                    :addable="addable" 
                    :sortable="sortable"
                    :removable="removable"
                    :collapsed-item-template="'<span> {{name}} </span>'"
                    :max-item-count="5"
                    item-id-attribute="id"
                    :read-only="readOnly"
                    locale="fr"
                    @input="inputEmitted" 
                />
            </div>
        `;
        MockI18n.mockLangFunction();
    });

    test('can mount empty list field', async () => {
        await createVm({
            propsData: {
                addable:true, removable:true, sortable:true
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount empty "read only" list field', async () => {
        await createVm({
            propsData: {
                readOnly: true,
                addable:true, removable:true, sortable:true
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount filled list field', async () => {
        await createVm({
            propsData: {
                addable:true, removable:true, sortable:true
            },
            data:()=>({
                value: [{id:0, name:'Antoine'}, {id:1, name:'Samuel'}]
            })
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "read only" filled list field', async () => {
        await createVm({
            propsData: {
                readOnly:true,
                addable: true, removable: true, sortable: true
            },
            data:()=>({
                value: [{id:0, name:'Antoine'}]
            })
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount collapsed list field', async () => {
        let $list = await createVm({
            propsData: {
                addable: true, removable: true, sortable: true
            },
            data:()=>({
                value: [{id:0, name:'Antoine'}]
            }),
        });

        expect.assertions(1);

        $list.dragActive = true;

        await Vue.nextTick();
        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount non addable, non removable, non sortable list field', async () => {
        await createVm({
            propsData: {
                addable: false, removable: false, sortable: false
            },
            data:()=>({
                value: [{id:0, name:'Antoine'},{ id:1, name:'Samuel' }]
            }),
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount with full list', async () => {
        await createVm({
            propsData: {
                addable:true, removable:true, sortable: true
            },
            data:()=>({
                value: [{id:0, name:'Antoine'},{id:1, name:'Samuel'},{id:2, name:'Solène'},{id:3, name:'Georges'},{id:4, name:'Gérard'}]
            }),
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('emit input on init to have list and value equals by reference (sync changes)', async () => {
        let inputEmitted = jest.fn();

        let $list = await createVm({
            methods: {
                inputEmitted
            }
        });

        expect(inputEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted).toHaveBeenCalledWith($list.list);
    });

    test('create appropriate item', async () => {
        let $list = await createVm();

        let item = $list.createItem();

        expect(item).toMatchObject({ id: null, name: null });
    });

    test('items have correct indexes', async () => {
        let $list = await createVm({
            data:()=>({
                value:[{id:0, name:'Antoine'},{id:1, name:'Samuel'},{id:2, name:'Solène'}]
            })
        });

        let { list, indexSymbol } = $list;

        expect(list).toMatchObject([
            { [indexSymbol]:0 },{ [indexSymbol]:1 },{ [indexSymbol]:2 }
        ]);

        $list.add();

        expect(list).toMatchObject([
            { [indexSymbol]:0 },{ [indexSymbol]:1 },{ [indexSymbol]:2 },{ [indexSymbol]:3 }
        ]);
    });

    test('items have correct drag indexes', async () => {
        let $list = await createVm({
            data:()=>({
                value:[{id:0, name:'Antoine'},{id:1, name:'Samuel'},{id:2, name:'Solène'}]
            })
        });

        $list.toggleDrag();

        let { list, dragIndexSymbol } = $list;

        expect(list).toMatchObject([
            { [dragIndexSymbol]:0 }, { [dragIndexSymbol]:1 }, { [dragIndexSymbol]:2 }
        ]);
    });

    test('remove item correctly', async () => {
        let $list = await createVm({
            data:()=>({
                value:[{id:0, name:'Antoine'},{id:1, name:'Samuel'},{id:2, name:'Solène'}]
            })
        });

        $list.remove(1);

        let { list, indexSymbol } = $list;

        expect(list).toMatchObject([
            {id:0, name:'Antoine', [indexSymbol]:0 },{id:2, name:'Solène', [indexSymbol]:2}
        ])
    });

    test('expose appropriate collapsed item template props data', async () => {
        let $list = await createVm();

        let { dragIndexSymbol } = $list;
        let data = $list.collapsedItemData({ id:1, name:'Samuel', [dragIndexSymbol]: 2 });

        expect(data).toMatchObject({
            $index: 2,
            id: 1,
            name: 'Samuel'
        });
    });

    test('expose appropriate props to field-display', () => {
        let wrapper = mount(List,{
            provide: MockInjections.provide,
            propsData: {
                value: [ { id:0, name:'myName' }],
                fieldLayout: {
                    item:[
                        [ {key:'name'} ]
                    ]
                },
                itemFields: { name: { type:'text' } },
                itemIdAttribute:"id",
            },
            methods: {
                update: jest.fn(i => `update ${i}`)
            }
        });
        let $field = wrapper.find(fieldDisplayMock);
        expect($field.vm.$options.propsData).toMatchObject({
            errorIdentifier: 'name'
        });
        expect($field.vm.$attrs).toEqual({
            'field-key': 'name',
            'context-fields': { name: { type:'text' } },
            'context-data': expect.objectContaining({ id: 0, name: 'myName', _fieldsLocale: {} }),
            'config-identifier': 'name',
            'update-data': 'update 0'
        });
    });

    test('update data properly', async () => {
        let $list = await createVm({
            provide: {
                $form: { localized:true, locales:['fr', 'en'] }
            },
            propsData: {
                itemFields: {
                    name: { key:'name', type:'text' },
                    localizedField: { key:'localizedField', type:'text', localized: true }
                },
            },

            data:()=>({
                value:[{id:0, name:'Antoine'},{id:1, name:'Samuel'},{id:2, name:'Solène'}]
            })
        }, null, { mockInjections:false });

        let updateFn = $list.update(1);
        $list.fieldLocalizedValue = jest.fn(()=>'fieldLocalizedValue');

        updateFn('name','George');
        expect($list.list[1]).toMatchObject({ id:1, name:'fieldLocalizedValue' });
        expect($list.fieldLocalizedValue).toHaveBeenCalledWith(
            'name', 'George',
            expect.objectContaining({id:1, name:'Samuel'}),
            { localizedField:'fr' }
        );

        jest.resetAllMocks();

        updateFn('localizedField', 'aaa');
        expect($list.list[1]).toMatchObject({ id:1, name:'fieldLocalizedValue', localizedField:undefined });
        expect($list.fieldLocalizedValue).toHaveBeenCalledWith(
            'localizedField', 'aaa',
            expect.objectContaining({id:1, name:'fieldLocalizedValue' }),
            { localizedField:'fr' }
        );
    });

    test('insert item properly', async () => {
        let $list = await createVm({
            data:()=>({
                value:[{id:0, name:'Antoine'},{id:2, name:'Solène'}]
            })
        });

        $list.insertNewItem(0, {});

        expect($list.list).toMatchObject([
            {id:0, name:'Antoine'},{id: null, name: null},{id:2, name:'Solène'}
        ])
    });

    test('have proper field identifier', async () => {
        let $list = await createVm({
            data:()=>({
                value:[{id:0, name:'Antoine'}, {id:1, name:'Samuel'}]
            })
        });

        let identifiers = $list.$findDeepChildren('SharpFieldContainer').map(fc => fc.mergedErrorIdentifier);

        expect(identifiers).toEqual(expect.arrayContaining([ '0.name', '1.name' ]));
    });

    test('has localize mixin with right fieldsProps', async () => {
        let $list = await createVm();
        expect($list.$options._localizedForm).toBe('itemFields');
    });
});

async function createVm(customOptions={}, mock, { mockInjections=true }={}) {

    const vm = new Vue({
        el: '#app',
        mixins: [
            mockInjections ? MockInjections: {},
            customOptions
        ],

        components: {
            'sharp-list':mock||List
        },

        props:['readOnly', 'addable', 'sortable', 'removable', 'itemFields'],

        'extends': {
            data:() => ({
                value: null
            }),
            methods: {
                inputEmitted: ()=>{}
            }
        }

    });

    await Vue.nextTick();

    return vm.$children[0];
}