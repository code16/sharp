import Vue from 'vue/dist/vue.common';
import DateField from '../components/form/fields/date/Date.vue';
import moment from 'moment';

import { FakeInjections, QueryComponent } from './utils';

describe('date-field',()=>{
    Vue.component('sharp-date', DateField);
    Vue.use(QueryComponent);

    beforeEach(() => {
        document.documentElement.lang = 'fr';
        document.body.innerHTML = `
            <div id="app">
                <sharp-date value="1996-08-20 12:11+02:00"
                            :has-date="!disableDate"
                            :has-time="!disableTime" 
                            :read-only="readOnly" 
                            :display-format="displayFormat"
                            :step-time="stepTime" 
                            :min-time="minTime" 
                            :max-time="maxTime"
                            @input="inputEmitted"
                            >
                </sharp-date>
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

    it('can mount "read only" Date field', async () => {
        await createVm({
            propsData: {
                readOnly: true,
                disableTime: true,
                disableDate: true
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('can mount "picker displayed" Date field', async () => {
        let $date = await createVm({
            propsData: {
                disableTime: true,
                disableDate: true
            }
        });

        $date.showPicker = true;

        await Vue.nextTick();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('expose appropriate props to TimePicker', async () => {
        let $date = await createVm({
            propsData: {
                displayFormat: 'HH : mm',
                stepTime: 10,
                minTime: '3:00',
                maxTime: '19:00'
            }
        });

        let timepicker = $date.$findChild('SharpTimePicker');

        $date.showPicker = true;

        await Vue.nextTick();

        expect(timepicker.$props).toMatchObject({
            value: {
                HH: '12',
                mm: '11',
                ss: '00'
            },
            active: true,
            format: 'HH : mm',
            minuteInterval: 10,
            min: '3:00',
            max: '19:00'
        })
    });

    it('expose appropriate props to DatePicker', async () => {
        let $date = await createVm();

        let datepicker = $date.$findChild('SharpDatepicker');

        expect(datepicker.$props).toMatchObject({
            value: new Date(1996, 7, 20, 12, 11),
            language: 'fr',
            inline: true,
            mondayFirst: true
        });
    });

    it('have correct input value', async () => {
        let $date = await createVm({
            propsData: {
                displayFormat: 'DD/MM/YYYY  HH : mm'
            }
        });

        expect($date.$refs.input.value).toBe('20/08/1996  12 : 11');
    });

    it('emit input on date changed & correct value', async () => {
        let $date = await createVm();

        let datepicker = $date.$findChild('SharpDatepicker');

        let inputEmitted = jest.fn();
        $date.$on('input', inputEmitted);
        datepicker.$emit('selected', new Date(1996, 7, 22));

        expect(inputEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted.mock.calls[0][0]).toBeInstanceOf(moment);
        expect(inputEmitted.mock.calls[0][0].toDate()).toEqual(new Date(1996, 7, 22, 12, 11));
    });

    it('emit input on time changed & correct value', async () => {
        let $date = await createVm();

        let timepicker = $date.$findChild('SharpTimePicker');

        let inputEmitted = jest.fn();
        $date.$on('input', inputEmitted);
        timepicker.$emit('change', { data: { HH: 13, mm: 20 } });

        expect(inputEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted.mock.calls[0][0]).toBeInstanceOf(moment);
        expect(inputEmitted.mock.calls[0][0].toDate()).toEqual(new Date(1996, 7, 20, 13, 20));
    });

    it('emit input on input changed', async () => {
        let $date = await createVm({
            displayFormat: 'DD/MM/YYYY HH:mm'
        });

        let { input } = $date.$refs;

        let inputEmitted = jest.fn();
        let okEmitted = jest.fn();

        $date.$on('input', inputEmitted);
        $date.$field.$on('ok', okEmitted);

        input.value = '22/08/1996 13:20';
        input.dispatchEvent(new Event('input', { bubbles: true }));

        expect(okEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted.mock.calls[0][0]).toBeInstanceOf(moment);
        expect(inputEmitted.mock.calls[0][0].toDate()).toEqual(new Date(1996, 7, 22, 13, 20));
    });

    it('emit error on input changed if invalid', async () => {
        let $date = await createVm({
            displayFormat: 'DD/MM/YYYY HH:mm'
        });

        let { input } = $date.$refs;

        let inputEmitted = jest.fn();
        let errorEmitted = jest.fn();

        $date.$field.$on('error', errorEmitted);
        $date.$on('input', inputEmitted);

        input.value = '20/08/1996 bug 12 : 40';
        input.dispatchEvent(new Event('input', { bubbles: true }));

        expect(errorEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted).toHaveBeenCalledTimes(0);
    });

    it('clear field state on blur', async () => {
        let $date = await createVm();

        let { input } = $date.$refs;

        let clearEmitted = jest.fn();

        $date.$field.$on('clear', clearEmitted);

        input.focus();
        input.blur();

        expect(clearEmitted).toHaveBeenCalled();
    });

    it('increase/decrease date on keydown up/down', async () => {
        let $date = await createVm({
            propsData: {
                displayFormat:'DD/MM/YYYY HH:mm'
            }
        });

        let { input } = $date.$refs;

        let inputEmitted = jest.fn();

        $date.$on('input', inputEmitted);

        input.setSelectionRange(1,1);
        input.dispatchEvent(new KeyboardEvent('keydown', { keyCode: 38 })); //UP

        expect(inputEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted.mock.calls[0][0]).toBeInstanceOf(moment);
        expect(inputEmitted.mock.calls[0][0].toDate()).toEqual(new Date(1997, 7, 20, 12, 10));
    });
});

async function createVm(customOptions={}) {
    let vm = new Vue({
        el: '#app',

        mixins: [customOptions, FakeInjections],
        props: ['readOnly', 'disableTime','disableDate' ,'displayFormat', 'stepTime', 'minTime', 'maxTime'],

        'extends': {
            methods: {
                inputEmitted:()=>{}
            }
        }
    });

    await Vue.nextTick();

    return vm.$children[0];
}