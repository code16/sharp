import Vue from 'vue';
import Tags from '../components/form/fields/Tags.vue';

import { MockI18n } from './utils';

describe('tags-field', () => {
    Vue.use(MockI18n);

    beforeEach(()=>{
        document.body.innerHTML = `
            <div id="app">
                <sharp-tags :value="value"
                            placeholder="placeholder"
                            :options="[{id:3, label:'AAA'}, {id:4, label:'BBB'}]" 
                            :max-tag-count="2"
                            :read-only="readOnly"
                            create-text="createText"
                            creatable
                            @input="inputEmitted($event)">
                </sharp-tags>
            </div>
        `;
        MockI18n.mockLangFunction();
    });

    it('can mout Tags field', async () => {
        await createVm();
        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('expose appropriate props to multiselect component', async () => {
        let $tags = await createVm({
            propsData: {
                readOnly: true,
                value: [{ id:3, label:'AAA'}]
            },
        });

        let { multiselect } = $tags.$refs;

        expect(multiselect.$props).toMatchObject({
            value: [{id:3, label:'AAA'}],
            placeholder: 'placeholder',
            tagPlaceholder: 'createText',
            taggable: true,
            max: 2,
            closeOnSelect: false,
            multiple: true,
            searchable: true,
            hideSelected: true,
            disabled: true
        });
    });

    it('instantiate proper internal ids', async () => {
        let $tags = await createVm({
            propsData: {
                value: [{ id:4, label:'BBB'}]
            },
        });

        expect($tags.tags[0].internalId).toEqual(1); // ( second option internal id )

        expect($tags.indexedOptions[0].internalId).toEqual(0);
        expect($tags.indexedOptions[1].internalId).toEqual(1);
    });

    it('adding a tag & with proper internal id', async () => {
        let $tags = await createVm({
            propsData: {
                value: []
            },
        });

        let { multiselect } = $tags.$refs;

        expect($tags.tags.length).toBe(0);

        multiselect.$emit('tag', 'CCC');

        await Vue.nextTick();

        expect($tags.tags.length).toBe(1);
        expect($tags.tags[0]).toEqual({ id:null, label:'CCC', _internalId: 2 });
    });

    it('emit a tag properly', async () => {
        let $tags = await createVm({
            propsData: {
                value: [{ id:4, label:'BBB'}]
            },
        });

        let { multiselect } = $tags.$refs;
        let inputEmitted = jest.fn();

        multiselect.$emit('tag', 'CCC');

        $tags.$on('input', inputEmitted);
        await Vue.nextTick();

        expect(inputEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted).toHaveBeenCalledWith([{ id:4, label:'BBB'},{ id:null, label:'CCC' }]);
    });

});

async function createVm(customOptions={}) {

    const vm = new Vue({
        el: '#app',
        mixins: [customOptions],

        components: {
            'sharp-tags': Tags
        },

        props:['value','readOnly'],

        'extends':{
           methods: {
               inputEmitted:()=>{}
           }
        },
    });

    await Vue.nextTick();

    return vm.$children[0];
}