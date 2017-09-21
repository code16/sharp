import Vue from 'vue/dist/vue.common';
import Autocomplete from '../components/form/fields/Autocomplete.vue';

import { nextRequestFulfilled } from './utils/moxios-utils';
import moxios from 'moxios';

import { MockI18n, wait } from './utils';


describe('autocomplete-field', ()=>{
    Vue.component('sharp-autocomplete', Autocomplete);
    Vue.use(MockI18n);

    describe('common & local', ()=> {
        beforeEach(()=>{
            document.body.innerHTML = `
            <div id="app">
                <sharp-autocomplete :value="value" 
                                    :read-only="readOnly"
                                    mode="local"
                                    placeholder="nom"
                                    item-id-attribute="id" 
                                    :search-keys="['name', 'alias']"
                                    :list-item-template="'<em>{{name}}, {{alias}}</em>'"
                                    :result-item-template="'<b>{{name}}, {{alias}}</b>'"
                                    :local-values="[{id:1, name:'Theodore Bagwell', alias:'T-Bag'},
                                                    {id:2, name:'Lincoln Burrows', alias: 'Linc'}]"
                                    @input="inputEmitted($event)"
                                    >
                </sharp-autocomplete>
            </div>`
        });

        it('can mount Autocomplete field as multiselect', async () => {
            await createVm();

            expect(document.body.innerHTML).toMatchSnapshot();
        });

        it('can Autocomplete field as result item', async () => {
            await createVm({
                data: () => ({ value: {id:1, name:'Theodore Bagwell', alias:'T-Bag'} })
            });

            expect(document.body.innerHTML).toMatchSnapshot();
        });

        it('expose appropriate props to multiselect component', async ()=>{
            let $tags = await createVm({
                propsData: {
                    readOnly: true
                },
                data: () => ({ value: null })
            });

            let { multiselect } = $tags.$refs;

            expect(multiselect.$props).toMatchObject({
                options: [{id: 1, name:'Theodore Bagwell', alias: 'T-Bag'},
                    {id: 2, name:'Lincoln Burrows', alias: 'Linc'}],
                value: null,
                multiple: false,
                trackBy: 'id',
                searchable: true,
                clearOnSelect: true,
                hideSelected: false,
                placeholder: 'nom',
                allowEmpty: true,
                resetAfter: false,
                closeOnSelect: true,
                taggable: false,
                preserveSearch: true,
                internalSearch: false,

                disabled: true,
            });
        });

        it('expose appropriate arguments to the local search engine', async () => {
            let $autocomplete = await createVm();


            expect($autocomplete.searchStrategy).toMatchObject({
                fuse: {
                    list: [{id:1, name:'Theodore Bagwell', alias:'T-Bag'}, {id:2, name:'Lincoln Burrows', alias: 'Linc'}]
                },
                options: {
                    keys: ['name', 'alias']
                }
            });
        });

        it('update autocomplete state properly', async () => {
            let $autocomplete = await createVm({
                data: () => ({ value: null })
            });

            let { $root:vm } = $autocomplete;
            let { multiselect } = $autocomplete.$refs;

            expect($autocomplete.state).toBe('initial');

            multiselect.$emit('search-change', 'T');
            expect($autocomplete.state).toBe('searching');

            multiselect.$emit('select', {id:1, name:'Theodore Bagwell', alias:'T-Bag'});
            vm.value = {id:1, name:'Theodore Bagwell', alias:'T-Bag'};
            expect($autocomplete.state).toBe('valuated');

            await Vue.nextTick();
            let clearBtn = document.querySelector('button');
            clearBtn.click();

            expect($autocomplete.state).toBe('initial');
        });

        it('is valuated on start if have value', async () => {
            let $autocomplete = await createVm({
                data: () => ({ value:{id:1, name:'Theodore Bagwell', alias:'T-Bag'} })
            });

            expect($autocomplete.state).toBe('valuated');
        });

        it('sync opened with multiselect', async () => {
            let $autocomplete = await createVm({
                data: () => ({ value:null })
            });
            let { multiselect } = $autocomplete.$refs;

            expect($autocomplete.opened).toBeFalsy();

            multiselect.$emit('open');

            expect($autocomplete.opened).toBe(true);

            multiselect.$emit('close');

            expect($autocomplete.opened).toBe(false);
        });

        it('emit input on select & correct value', async () => {
            let inputEmitted = jest.fn();

            let $autocomplete = await createVm({
                data: () => ({ value:null }),
                methods: {
                    inputEmitted
                }
            });
            let { $root:vm } = $autocomplete;
            let { multiselect } = $autocomplete.$refs;

            multiselect.$emit('select', {id:2, name:'Lincoln Burrows', alias: 'Linc'});

            expect(inputEmitted).toHaveBeenLastCalledWith({id:2, name:'Lincoln Burrows', alias: 'Linc'});
        });

        it('define loading slot', async () => {
            let $autocomplete = await createVm();
            let { multiselect } = $autocomplete.$refs;
            expect(multiselect.$slots.loading).toBeTruthy();
        });

        it('define noResult slot', async () => {
            let $autocomplete = await createVm();
            let { multiselect } = $autocomplete.$refs;
            expect(multiselect.$slots.loading).toBeTruthy();
        });
    });

    describe('remote', () => {
        beforeEach(()=>{
            document.body.innerHTML = `
            <div id="app">
                <sharp-autocomplete :value="value" 
                                    :read-only="readOnly" 
                                    mode="remote"
                                    placeholder="nom"
                                    item-id-attribute="id" 
                                    remote-endpoint="/autocomplete"
                                    :search-min-chars="3"
                                    :remote-method="remoteMethod"
                                    remote-search-attribute="search"
                                    :list-item-template="'<em>{{name}}, {{alias}}</em>'"
                                    :result-item-template="'<b>{{name}}, {{alias}}</b>'"
                                    @input="inputEmitted($event)">
                </sharp-autocomplete>
            </div>`;
            moxios.install();
        });

        afterEach(()=>{
            moxios.uninstall();
        });

        it("'loading' computed property changes properly", async () => {
            let $autocomplete = await createVm({
                propsData: {
                    remoteMethod: 'POST'
                }
            });

            expect.assertions(6);

            let { multiselect } = $autocomplete.$refs;

            expect($autocomplete.isLoading).toBeFalsy();

            $autocomplete.state = 'loading';
            expect($autocomplete.isLoading).toBe(true);

            $autocomplete.state = 'initial';
            expect($autocomplete.isLoading).toBe(false);

            multiselect.$emit('open');
            multiselect.$emit('search-change','Li'); // less than min chars
            expect($autocomplete.isLoading).toBe(true);

            multiselect.$emit('search-change','Linc');
            expect($autocomplete.isLoading).toBe(true);

            await nextRequestFulfilled({ status: 200, response:[] }, 210);

            expect($autocomplete.isLoading).toBe(false);
        });

        it("'hide dropdown' computed property changes properly (query less than min chars)", async () => {
            let $autocomplete = await createVm();

            let { multiselect } = $autocomplete.$refs;

            expect($autocomplete.hideDropdown).toBe(true);

            multiselect.$emit('search-change', 'Th');
            expect($autocomplete.hideDropdown).toBe(true);

            multiselect.$emit('search-change', 'The');
            expect($autocomplete.hideDropdown).toBe(false);

        });

        it('Update autocomplete state after request', async () => {
            let $autocomplete = await createVm();

            let { multiselect } = $autocomplete.$refs;

            expect($autocomplete.state).toBe('initial');

            multiselect.$emit('search-change', 'Theo');
            await nextRequestFulfilled({ status:200, response:[] }, 210);

            expect($autocomplete.state).toBe('searching');
        });

        it('Load response data', async () => {
            let $autocomplete = await createVm();

            let { multiselect } = $autocomplete.$refs;

            multiselect.$emit('search-change', 'Theo');
            await nextRequestFulfilled({ status:200, response:[{id:1, name:'Theodore Bagwell', alias:'T-Bag'}] }, 210);

            expect($autocomplete.suggestions).toEqual([{id:1, name:'Theodore Bagwell', alias:'T-Bag'}]);
        });

        it("Send proper GET request", async () => {
            let $autocomplete = await createVm({
                propsData: {
                    remoteMethod: 'GET'
                }
            });

            let { multiselect } = $autocomplete.$refs;

            multiselect.$emit('search-change', 'Linc');

            let { request } = await nextRequestFulfilled({ status:200, response:[] }, 210);
            let { config:{ method, params, url }} = request;

            expect(method).toBe('get');
            expect(params).toEqual({ search: 'Linc' });
            expect(url).toBe('/autocomplete');
        });

        it("Send proper POST request", async () => {
            let $autocomplete = await createVm({
                propsData: {
                    remoteMethod: 'POST'
                }
            });

            let { multiselect } = $autocomplete.$refs;

            multiselect.$emit('search-change', 'Linc');

            let { request } = await nextRequestFulfilled({ status:200, response:[] }, 400);
            let { config:{ method, data, url }} = request;

            expect(method).toBe('post');
            expect(JSON.parse(data)).toEqual({ search: 'Linc' });
            expect(url).toBe('/autocomplete');
        });
    });

});

async function createVm(customOptions={}) {
    const vm = new Vue({
        el: '#app',
        mixins: [customOptions],

        props:['readOnly', 'remoteMethod'],

        'extends': {
            data:()=>({ value: null }),
            methods: {
                inputEmitted:()=>{}
            }
        }
    });

    await Vue.nextTick();

    return vm.$children[0];
}