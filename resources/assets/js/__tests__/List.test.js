import Vue from 'vue';
import List from '../components/form/fields/list/List.vue';

import { MockInjections, MockTransitions, MockI18n, QueryComponent } from './utils';
import { ErrorNode } from '../mixins';

describe('list-field', () => {
    Vue.use(MockTransitions);
    Vue.use(MockI18n);
    Vue.use(QueryComponent);

    Vue.component('sharp-field-display', {
        name: 'SharpFieldContainer',
        inheritAttrs: false,
        mixins: [ ErrorNode ],
        template: '<div> FIELD DISPLAY MOCK </div>'
    });

    beforeEach(()=>{
        document.body.innerHTML = `    
            <div id="app">
                <sharp-list :value="value" 
                            :field-layout="{ 
                                item:[
                                    [ {key:'name'} ]
                                ]
                            }"
                            :item-fields="{ name: { type:'text' } }"
                            :addable="addable" 
                            :sortable="sortable"
                            :removable="removable"
                            :collapsed-item-template="'<span> {{name}} </span>'"
                            :max-item-count="5"
                            item-id-attribute="id"
                            :read-only="readOnly"
                            locale="fr"
                            @input="inputEmitted">
                </sharp-list>
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

    test('update data properly', async () => {
        let $list = await createVm({
            data:()=>({
                value:[{id:0, name:'Antoine'},{id:1, name:'Samuel'},{id:2, name:'Solène'}]
            })
        });

        let updateFn = $list.update(1);

        updateFn('name','George');

        expect($list.list[1]).toMatchObject({
            id:1, name:'George'
        })
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
});

async function createVm(customOptions={}) {

    const vm = new Vue({
        el: '#app',
        mixins: [MockInjections, customOptions],

        components: {
            'sharp-list':List
        },

        props:['readOnly', 'addable', 'sortable', 'removable'],

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