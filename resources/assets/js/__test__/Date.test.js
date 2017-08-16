import Vue from 'vue/dist/vue.common';
import Date from '../components/form/fields/date/Date.vue';

import { FakeInjections } from './utils';

describe('date-field',()=>{
    Vue.component('sharp-date', Date);

    beforeEach(() => {
        document.documentElement.lang = 'fr';
        document.body.innerHTML = `
            <div id="app">
                <sharp-date value="2013-02-08 09:30:26.123+02:00" :has-time="!disableTime" :read-only="readOnly"></sharp-date>
            </div>
        `
    });

    it('can mount Date field', async () => {
        await createVm();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('can mount Date field without TimePicker', async () => {
        await createVm({
            propsData: {
                disableTime: true
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('expose appropriate props to TimePicker', async () => {
        let $date = await createVm();
    });
});

async function createVm() {
    let vm = new Vue({
        el: '#app',

        mixins: [FakeInjections],
        props: ['readOnly', 'disableTime']
    });

    await Vue.nextTick();

    return vm.$children[0];
}