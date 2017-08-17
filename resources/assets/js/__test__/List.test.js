import Vue from 'vue/dist/vue.common';
import List from '../components/form/fields/list/List.vue';
import FieldDisplay from '../components/form/FieldDisplay';

import { MockInjections, MockTransitions } from './utils';

describe('list-field', () => {
    Vue.component('sharp-list', List);
    Vue.component('sharp-field-display', FieldDisplay);

    Vue.use(MockTransitions);

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
                            @input="inputEmitted">
                </sharp-list>
            </div>
        `
    });

    it('can mount empty list field', async () => {
        await createVm();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('can mount filled list field', async () => {
        await createVm({
            data:()=>({
                value: [{id:0, name:'Antoine'}]
            })
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('can mount collapsed list field', async () => {
        let $list = await createVm({
            data:()=>({
                value: [{id:0, name:'Antoine'}]
            }),
        });

        $list.dragActive = true;

        await Vue.nextTick();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('emit input on init to have list and value equals by reference (sync changes)', async () => {
        let inputEmitted = jest.fn();

        let $list = await createVm({
            methods: {
                inputEmitted
            }
        });


        expect(inputEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted).toHaveBeenCalledWith($list.list);
    });
});

async function createVm(customOptions={}) {

    const vm = new Vue({
        el: '#app',
        mixins: [MockInjections, customOptions],

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