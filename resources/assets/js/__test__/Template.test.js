import Vue from 'vue/dist/vue.common.js';
import Template from '../components/Template.vue';



describe('template-component',()=>{
    Vue.component('sharp-template', Template);

    beforeEach(()=>{
        document.body.innerHTML = `
            <div id="app">
                <sharp-template name="Test" :template-data="{ name:'Antoine' }" template="<em v-text='name'></em>"></sharp-template>
            </div>
        `
    });

    it('can mount given template wrapped in div tag', async () => {
        await createVm();

        expect(document.body.innerHTML).toMatchSnapshot();
    });
});


async function createVm(customOptions={}) {
    const vm = new Vue({
        el: '#app',
        mixins: [customOptions],
    });

    await Vue.nextTick();

    return vm.$children[0];
}