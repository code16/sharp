import Vue from 'vue';
import Autocomplete from '../components/form/fields/Autocomplete.vue';

import { nextRequestFulfilled } from './test-utils/moxios-utils';
import moxios from 'moxios';

import { MockI18n } from './test-utils';


describe('autocomplete-field', ()=>{
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
            </div>`;
            MockI18n.mockLangFunction();
        });

        test('can mount Autocomplete field as multiselect', async () => {
            await createVm();

            expect(document.body.innerHTML).toMatchSnapshot();
        });

        test('can mount Autocomplete field as result item', async () => {
            await createVm({
                data: () => ({ value: {id:1, name:'Theodore Bagwell', alias:'T-Bag'} })
            });

            expect(document.body.innerHTML).toMatchSnapshot();
        });

        test('expose appropriate props to multiselect component', async ()=>{
            let $autocomplete = await createVm({
                propsData: {
                    readOnly: true
                },
                data: () => ({ value: null })
            });

            let { multiselect } = $autocomplete.$refs;

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

        test('expose appropriate arguments to the local search engine', async () => {
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


        test('sync opened with multiselect', async () => {
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

        test('emit input on select & correct value', async () => {
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

        test('define loading slot', async () => {
            let $autocomplete = await createVm();
            let { multiselect } = $autocomplete.$refs;
            expect(multiselect.$slots.loading).toBeTruthy();
        });

        test('define noResult slot', async () => {
            let $autocomplete = await createVm();
            let { multiselect } = $autocomplete.$refs;
            expect(multiselect.$slots.loading).toBeTruthy();
        });
    });

    describe('remote', () => {
        beforeEach(()=>{
            document.body.innerHTML = `
            <div id="app">
                <sharp-autocomplete 
                    :value="value" 
                    :read-only="readOnly" 
                    mode="remote"
                    placeholder="nom"
                    item-id-attribute="id" 
                    remote-endpoint="/autocomplete"
                    :search-min-chars="3"
                    :remote-method="remoteMethod || 'GET'"
                    remote-search-attribute="search"
                    :list-item-template="'<em>{{name}}, {{alias}}</em>'"
                    :result-item-template="'<b>{{name}}, {{alias}}</b>'"
                    @input="inputEmitted($event)"
                />
            </div>`;
            moxios.install();
        });

        afterEach(()=>{
            moxios.uninstall();
        });

        test("'loading' computed property changes properly", async () => {
            let $autocomplete = await createVm({
                propsData: {
                    remoteMethod: 'POST'
                }
            });

            let { multiselect } = $autocomplete.$refs;

            expect($autocomplete.isLoading).toBeFalsy();

            multiselect.$emit('open');
            multiselect.$emit('search-change','Li'); // less than min chars
            expect($autocomplete.isLoading).toBe(false);

            multiselect.$emit('search-change','Linc');
            expect($autocomplete.isLoading).toBe(true);

            await nextRequestFulfilled({ status: 200, response:[] }, 210);

            expect($autocomplete.isLoading).toBe(false);
        });

        test("'hide dropdown' computed property changes properly (query less than min chars)", async () => {
            let $autocomplete = await createVm();

            let { multiselect } = $autocomplete.$refs;

            expect($autocomplete.hideDropdown).toBe(true);

            multiselect.$emit('search-change', 'Th');
            expect($autocomplete.hideDropdown).toBe(true);

            multiselect.$emit('search-change', 'The');
            expect($autocomplete.hideDropdown).toBe(false);

        });

        test('Load response data', async () => {
            let $autocomplete = await createVm();

            let { multiselect } = $autocomplete.$refs;

            multiselect.$emit('search-change', 'Theo');
            await nextRequestFulfilled({ status:200, response:[{id:1, name:'Theodore Bagwell', alias:'T-Bag'}] }, 210);

            expect($autocomplete.suggestions).toEqual([{id:1, name:'Theodore Bagwell', alias:'T-Bag'}]);
        });

        test("Send proper GET request", async () => {
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

        test("Send proper POST request", async () => {
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

        components: {
            'sharp-autocomplete': Autocomplete
        },

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