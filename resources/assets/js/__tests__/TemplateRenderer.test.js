import Vue from 'vue';
import TemplateRenderer from '../components/TemplateRenderer';



describe('template-renderer',()=>{
    Vue.component('sharp-template', TemplateRenderer);

    beforeEach(()=>{
        document.body.innerHTML = `
            <div id="app">
                <sharp-template name="Test" :template-data="{ name:'Antoine' }" :template="'<em>{{name}}</em>'"></sharp-template>
            </div>
        `
    });

    test('can mount given template wrapped in div tag', async () => {
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