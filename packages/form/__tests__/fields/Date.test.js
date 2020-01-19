import Vue from 'vue';
import SharpDate from '../../components/fields/date/Date.vue';
import moment from 'moment-timezone';

import { MockInjections, MockI18n } from 'sharp-test-utils';


function date(...args) {
    return new Date(Date.UTC(...args));
}

describe('date-field',()=>{
    Vue.component('sharp-date', {
        extends: SharpDate,
        components: {
            BPopover: {
                template:'<div><slot v-bind="{}" /></div>',
            }
        }
    });

    moment.tz.setDefault('UTC');

    beforeEach(() => {
        document.documentElement.lang = 'fr';
        document.body.innerHTML = `
            <div id="app">
                <sharp-date :value="typeof value == 'undefined' ? 
                                    '1996-08-20 12:11+00:00' : value"
                            :has-date="!disableDate"
                            :has-time="!disableTime" 
                            :read-only="readOnly" 
                            :display-format="displayFormat"
                            :step-time="stepTime" 
                            :min-time="minTime" 
                            :max-time="maxTime"
                            :monday-first="mondayFirst"
                            @input="inputEmitted"
                            >
                </sharp-date>
                <div ref="outsideElement"></div>
            </div>
        `;
        MockI18n.mockLangFunction();
    });

    test('can mount Date field', async () => {
        await createVm();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount Date field without TimePicker', async () => {
        await createVm({
            propsData: {
                disableTime: true
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "read only" Date field', async () => {
        await createVm({
            propsData: {
                readOnly: true,
                disableTime: true,
                disableDate: true
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "picker displayed" Date field', async () => {
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

    test('can mount "monday first" Date field', async () => {
        let $date = await createVm({
            propsData: {
                mondayFirst: true,
            }
        });

        $date.showPicker = true;

        await Vue.nextTick();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('expose appropriate props to TimePicker', async () => {
        let $date = await createVm({
            propsData: {
                displayFormat: 'HH : mm',
                stepTime: 10,
                minTime: '3:00',
                maxTime: '19:00'
            }
        });

        let timepicker = $date.$refs.timepicker;

        $date.showPicker = true;

        await Vue.nextTick();

        expect(timepicker.$props).toMatchObject({
            value: {
                HH: '12',
                mm: '11'
            },
            active: true,
            format: 'HH : mm',
            minuteInterval: 10,
            min: '3:00',
            max: '19:00'
        })
    });

    test('expose appropriate props to DatePicker', async () => {
        let $date = await createVm();

        let datepicker = $date.$refs.datepicker;

        expect(datepicker.$props).toMatchObject({
            value: date(1996, 7, 20, 12, 11),
            language: 'fr',
            inline: true,
            mondayFirst: undefined,
        });
    });

    test('have correct input value', async () => {
        let $date = await createVm({
            propsData: {
                displayFormat: 'DD/MM/YYYY  HH : mm'
            }
        });

        expect($date.$refs.input.value).toBe('20/08/1996  12 : 11');
    });

    test('emit input on date changed & correct value', async () => {
        let $date = await createVm();

        let datepicker = $date.$refs.datepicker;

        let inputEmitted = jest.fn();
        $date.$on('input', inputEmitted);
        datepicker.$emit('selected', date(1996, 7, 22));

        expect(inputEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted.mock.calls[0][0]).toBeInstanceOf(moment);
        expect(inputEmitted.mock.calls[0][0].toDate()).toEqual(date(1996, 7, 22, 12, 11));
    });

    test('emit input on time changed & correct value', async () => {
        let $date = await createVm();

        let timepicker = $date.$refs.timepicker;

        let inputEmitted = jest.fn();
        $date.$on('input', inputEmitted);
        timepicker.$emit('change', { data: { HH: 13, mm: 20 } });

        expect(inputEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted.mock.calls[0][0]).toBeInstanceOf(moment);
        expect(inputEmitted.mock.calls[0][0].toDate()).toEqual(date(1996, 7, 20, 13, 20));
    });

    test('emit input on input changed and show picker', async () => {
        let $date = await createVm({
            displayFormat: 'DD/MM/YYYY HH:mm'
        });

        let { input } = $date.$refs;

        let inputEmitted = jest.fn();
        let clearEmitted = jest.fn();

        $date.$on('input', inputEmitted);
        $date.$field.$on('clear', clearEmitted);

        $date.showPicker = false;

        input.value = '22/08/1996 13:20';
        input.dispatchEvent(new Event('input', { bubbles: true }));

        expect(clearEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted.mock.calls[0][0]).toBeInstanceOf(moment);
        expect(inputEmitted.mock.calls[0][0].toDate()).toEqual(date(1996, 7, 22, 13, 20));
        expect($date.showPicker).toBe(true);
    });

    test('emit error on input changed if invalid and hide picker', async () => {
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

        expect(inputEmitted).toHaveBeenCalledTimes(0);
        expect(errorEmitted).toHaveBeenCalledTimes(1);
        expect($date.showPicker).toBe(false);
    });

    test('keep custom user input', async () => {
        let $date = await createVm({
            displayFormat: 'DD/MM/YYYY HH:mm'
        });
        let { input } = $date.$refs;

        input.value = '20/08/1996 bug 12 : 40';
        input.dispatchEvent(new Event('input', { bubbles: true }));
        $date.$forceUpdate();

        await Vue.nextTick();

        expect(input.value).toBe('20/08/1996 bug 12 : 40');
    });

    test('rollback custom user input on blur', async () => {
        let $date = await createVm({
            displayFormat: 'DD/MM/YYYY HH:mm'
        });
        let { input } = $date.$refs;

        input.value = '20/08/1996 bug 12 : 40';
        input.dispatchEvent(new Event('input', { bubbles: true }));

        input.focus();
        input.blur();

        $date.$forceUpdate();
        await Vue.nextTick();

        expect(input.value).toBe('20/08/1996 12:11');
    });

    test('rollback custom user input on clear button', async () => {
        let $date = await createVm({
            displayFormat: 'DD/MM/YYYY HH:mm',
        });
        let { input, clearButton } = $date.$refs;

        input.value = '20/08/1996 bug 12 : 40';
        input.dispatchEvent(new Event('input', { bubbles: true }));

        clearButton.click();

        $date.$forceUpdate();
        await Vue.nextTick();

        expect(input.value).toBe('20/08/1996 12:11');
    });

    test('clear when click on clear button', async () => {
        let $date = await createVm();

        let { input, clearButton } = $date.$refs;
        let { $root: vm } = $date;

        let clearEmitted = jest.fn();
        let inputEmitted = jest.fn();

        $date.$field.$on('clear', clearEmitted);
        $date.$on('input', inputEmitted);

        clearButton.click();
        vm.value = null;

        await Vue.nextTick();

        expect(clearEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted).toHaveBeenCalledWith(null);
        expect(input.value).toBe('');
    });

    test('clear field state on blur', async () => {
        let $date = await createVm();

        let { input } = $date.$refs;

        let clearEmitted = jest.fn();

        $date.$field.$on('clear', clearEmitted);

        input.focus();
        input.blur();

        expect(clearEmitted).toHaveBeenCalled();
    });

    test('increase/decrease date on keydown up/down', async () => {
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

    test('increase/decrease minute properly', async () => {
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

    test('select text properly after increase/descrease', async () => {
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

    test('time only value', async () => {
        let $date = await createVm({
            propsData: {
                disableDate: true
            },
            data:()=>({ value:'13:00' })
        });

        expect($date.timeObject).toEqual({
            HH: '13',
            mm: '00'
        })
    });

    test('date only value', async () => {
        let $date = await createVm({
            propsData: {
                disableTime: true
            },
            data:()=>({ value:'2018-05-20' })
        });

        expect($date.dateObject).toEqual(date(2018, 4, 20));
    });
});

async function createVm(customOptions={}) {
    let vm = new Vue({
        el: '#app',

        mixins: [customOptions, MockInjections],
        props: ['readOnly', 'disableTime','disableDate' ,'displayFormat', 'stepTime', 'minTime', 'maxTime','mondayFirst'],

        'extends': {
            methods: {
                inputEmitted:()=>{}
            },
            data: ()=>({ value: undefined })
        }
    });

    await Vue.nextTick();

    return vm.$children[0];
}