import Vue from 'vue/dist/vue.common';
import Autocomplete from '../components/form/fields/Autocomplete.vue';


describe('autocomplete-field', ()=>{
    Vue.component('sharp-autocomplete', Autocomplete);

    beforeEach(()=>{
        document.body.innerHTML = `
            <div id="app">
                <sharp-autocomplete :value="value" :read-only="readOnly" mode="local" placeholder="nom"
                                    item-id-attribute="id" list-item-template="<em>{{name}}</em>"
                                    result-item-template="<b>{{name}}</b>"
                                    :local-values="[{id: 1, name:'Antoine'},{id:2, name:'Samuel'}]">
                </sharp-autocomplete>
            </div>
        `
    });

    it('can mount Autocomplete field', async () => {
        await createVm();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('expose appropriate props to multiselect component', async ()=>{
        let $tags = await createVm({
            propsData: {
                readOnly: true
            },
            data: () => ({ value: [3] })
        });

        let { multiselect } = $tags.$refs;

        expect(multiselect.$props).toMatchObject({
            value: [3],
            placeholder: 'placeholder',
            max: 3,
            disabled: true,
            searchable: false,
            // multiple dependant props
            closeOnSelect: false,
            multiple: true,
            hideSelected: true,
        });
    });

});

async function createVm(customOptions={}) {
    const vm = new Vue({
        el: '#app',
        mixins: [customOptions],

        props:['value','readOnly'],

        'extends': {
            data:()=>({ value: null })
        }
    });

    await Vue.nextTick();

    return vm.$children[0];
}