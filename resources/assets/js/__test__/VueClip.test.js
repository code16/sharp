import Vue from 'vue/dist/vue.common';
import VueClip from '../components/form/fields/upload/VueClip';
import { MockInjections, MockI18n } from "./utils/index";


describe('vue-clip',() => {
    Vue.use(MockI18n);

    beforeEach(()=>{
        document.body.innerHTML = `
            <div id="app">
                <sharp-vue-clip :value="value"
                    :options="{ url:'/' }"
                    download-id="my_upload" 
                    pending-key="my_upload.0" 
                    :ratio-x="1" 
                    :ratio-y="2"
                    :read-only="readOnly">
                </sharp-vue-clip>
            </div>
        `;
        MockI18n.mockLangFunction();
    });

    it('can mount VueClip component', async () => {
        await createVm();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('can mount read-only VueClip component', async () => {
        await createVm({
            propsData: {
                readOnly: true
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('can mount initially non image VueClip component', async () => {
        await createVm({
            data: () => ({
                value: {
                    name: 'Fichier.pdf',
                    size: 15000
                }
            })
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('can mount initially image VueClip component', async () => {
        await createVm({
            data: () => ({
                value: {
                    name: 'Image.jpg',
                    thumbnail: '/image.jpg',
                    size: 15000
                }
            })
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

});

async function createVm(customOptions={}) {
    const vm = new Vue({
        el: '#app',
        mixins: [customOptions, MockInjections],

        components: {
            'sharp-vue-clip': {
                'extends': VueClip,
                components: {
                    'vue-cropper': { render:() => {} }
                }
            }
        },

        props:['readOnly'],

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