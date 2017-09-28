import Vue from 'vue';
import DateField from '../components/form/fields/date/Date.vue';
import moment from 'moment-timezone';

import { MockInjections, QueryComponent } from './utils';


function date(...args) {
    return new Date(Date.UTC(...args));
}

describe('date-field',()=>{
    Vue.component('sharp-date', DateField);
    Vue.use(QueryComponent);

    moment.tz.setDefault('UTC');

    beforeEach(() => {
        document.documentElement.lang = 'fr';
        document.body.innerHTML = `
            <div id="app">
                <sharp-date value="1996-08-20 12:11+00:00"
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
                <div ref="outsideElement"></div>
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
            value: date(1996, 7, 20, 12, 11),
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
        datepicker.$emit('selected', date(1996, 7, 22));

        expect(inputEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted.mock.calls[0][0]).toBeInstanceOf(moment);
        expect(inputEmitted.mock.calls[0][0].toDate()).toEqual(date(1996, 7, 22, 12, 11));
    });

    it('emit input on time changed & correct value', async () => {
        let $date = await createVm();

        let timepicker = $date.$findChild('SharpTimePicker');

        let inputEmitted = jest.fn();
        $date.$on('input', inputEmitted);
        timepicker.$emit('change', { data: { HH: 13, mm: 20 } });

        expect(inputEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted.mock.calls[0][0]).toBeInstanceOf(moment);
        expect(inputEmitted.mock.calls[0][0].toDate()).toEqual(date(1996, 7, 20, 13, 20));
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
        expect(inputEmitted.mock.calls[0][0].toDate()).toEqual(date(1996, 7, 22, 13, 20));
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
                displayFormat:'DD/MM/YYYY HH:mm',
                stepTime: 10
            }
        });

        let { input } = $date.$refs;

        let inputEmitted = jest.fn();

        $date.$on('input', inputEmitted);


        // Day change
        input.setSelectionRange(1,1);

        input.dispatchEvent(new KeyboardEvent('keydown', { keyCode: 38 })); //UP
        expect(inputEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted.mock.calls[0][0]).toBeInstanceOf(moment);
        expect(inputEmitted.mock.calls[0][0].toDate()).toEqual(date(1996, 7, 21, 12, 11));

        input.dispatchEvent(new KeyboardEvent('keydown', { keyCode: 40 })); //DOWN
        expect(inputEmitted).toHaveBeenCalledTimes(2);
        expect(inputEmitted.mock.calls[1][0]).toBeInstanceOf(moment);
        expect(inputEmitted.mock.calls[1][0].toDate()).toEqual(date(1996, 7, 20, 12, 11));


        // Month change
        input.setSelectionRange(3,3);

        input.dispatchEvent(new KeyboardEvent('keydown', { keyCode: 38 })); //UP
        expect(inputEmitted).toHaveBeenCalledTimes(3);
        expect(inputEmitted.mock.calls[2][0].toDate()).toEqual(date(1996, 8, 20, 12, 11));

        input.dispatchEvent(new KeyboardEvent('keydown', { keyCode: 40 })); //DOWN
        expect(inputEmitted).toHaveBeenCalledTimes(4);
        expect(inputEmitted.mock.calls[3][0].toDate()).toEqual(date(1996, 7, 20, 12, 11));


        // Year change
        input.setSelectionRange(6,6);

        input.dispatchEvent(new KeyboardEvent('keydown', { keyCode: 38 })); //UP
        expect(inputEmitted).toHaveBeenCalledTimes(5);
        expect(inputEmitted.mock.calls[4][0].toDate()).toEqual(date(1997, 7, 20, 12, 11));

        input.dispatchEvent(new KeyboardEvent('keydown', { keyCode: 40 })); //DOWN
        expect(inputEmitted).toHaveBeenCalledTimes(6);
        expect(inputEmitted.mock.calls[5][0].toDate()).toEqual(date(1996, 7, 20, 12, 11));


        // Hours change
        input.setSelectionRange(11,11);

        input.dispatchEvent(new KeyboardEvent('keydown', { keyCode: 38 })); //UP
        expect(inputEmitted).toHaveBeenCalledTimes(7);
        expect(inputEmitted.mock.calls[6][0].toDate()).toEqual(date(1996, 7, 20, 13, 11));

        input.dispatchEvent(new KeyboardEvent('keydown', { keyCode: 40 })); //DOWN
        expect(inputEmitted).toHaveBeenCalledTimes(8);
        expect(inputEmitted.mock.calls[7][0].toDate()).toEqual(date(1996, 7, 20, 12, 11));


        // Minutes change
        input.setSelectionRange(14,14);

        input.dispatchEvent(new KeyboardEvent('keydown', { keyCode: 38 })); //UP
        expect(inputEmitted).toHaveBeenCalledTimes(9);
        expect(inputEmitted.mock.calls[8][0].toDate()).toEqual(date(1996, 7, 20, 12, 20));

        input.dispatchEvent(new KeyboardEvent('keydown', { keyCode: 40 })); //DOWN
        expect(inputEmitted).toHaveBeenCalledTimes(10);
        expect(inputEmitted.mock.calls[9][0].toDate()).toEqual(date(1996, 7, 20, 12, 10));
    });

    it('increase/decrease minute properly', async () => {
        let $date = await createVm({
            propsData: {
                displayFormat:'DD/MM/YYYY HH:mm',
                stepTime: 10
            }
        });

        let { input } = $date.$refs;

        let inputEmitted = jest.fn();

        $date.$on('input', inputEmitted);

        input.setSelectionRange(14,14);
        input.dispatchEvent(new KeyboardEvent('keydown', { keyCode: 38 })); //UP
        expect(inputEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted.mock.calls[0][0].toDate()).toEqual(date(1996, 7, 20, 12, 20));

        input.value = '20/08/1996 12:11';
        input.dispatchEvent(new Event('input', { bubbles: true }));

        input.setSelectionRange(14,14);
        input.dispatchEvent(new KeyboardEvent('keydown', { keyCode: 40 })); //DOWN
        expect(inputEmitted).toHaveBeenCalledTimes(3);
        expect(inputEmitted.mock.calls[2][0].toDate()).toEqual(date(1996, 7, 20, 12, 10));
    })

    it('select text properly after increase/descrease', async () => {
        let $date = await createVm({
            propsData: {
                displayFormat:'DD/MM/YYYY HH:mm',
            }
        });

        let { input } = $date.$refs;

        input.setSelectionRange(1,1);
        input.setSelectionRange = jest.fn();
        input.dispatchEvent(new KeyboardEvent('keydown', { keyCode: 38 })); //UP

        await Vue.nextTick();

        expect(input.setSelectionRange).toHaveBeenCalledTimes(1);
        expect(input.setSelectionRange).toHaveBeenCalledWith(0,2);
    });

    it('show on focus', async () => {
        let $date = await createVm();

        let { input } = $date.$refs;

        input.focus();

        expect($date.showPicker).toBe(true);
    });

    it('hide on blur', async () => {
        let $date = await createVm();

        let { input } = $date.$refs;

        input.focus();
        input.blur();

        expect($date.showPicker).toBe(false);
    });
});

async function createVm(customOptions={}) {
    let vm = new Vue({
        el: '#app',

        mixins: [customOptions, MockInjections],
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