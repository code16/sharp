import Vue from 'vue/dist/vue';
import Text from '../components/form/fields/Text.vue';



describe('text-fields', () => {
    Vue.component('sharp-text', Text);

    beforeEach(()=>{
        document.body.innerHTML = `
            <div id="app">
                <sharp-text placeholder="Entrez du texte" value="AAA"></sharp-text>
            </div>
        `
    });

    it('can mount Text field', async () => {
        await createVm();

        expect(document.body.innerHTML).toMatchSnapshot();
    });
});

async function createVm() {
    const vm = new Vue({
        el: '#app',
    });

    await Vue.nextTick();

    return vm.$children[0];
}