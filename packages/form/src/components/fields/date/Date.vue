<template>
    <div class="SharpDate" :class="{'SharpDate--open':showPicker}">
        <div class="SharpDate__input-wrapper position-relative">
            <div class="input-group" :class="{ 'input-group--clearable': hasClearButton }" ref="inputGroup">
                <button class="input-group-text btn SharpDate__prepend"
                    @pointerdown="handlePrependButtonPointerDown"
                    @click="handlePrependButtonClicked"
                >
                    <svg class="align-middle" width="1.25em" height="1.25em" viewBox="0 0 32 32" style="fill:currentColor">
                        <path d="M26,4h-4V2h-2v2h-8V2h-2v2H6C4.9,4,4,4.9,4,6v20c0,1.1,0.9,2,2,2h20c1.1,0,2-0.9,2-2V6C28,4.9,27.1,4,26,4z M26,26H6V12h20  V26z M26,10H6V6h4v2h2V6h8v2h2V6h4V10z"/>
                    </svg>
                </button>
                <template v-if="displayFormat">
                    <input
                        :id="id"
                        class="form-control clearable SharpDate__input"
                        :class="{ 'SharpDate__input--valuated': value }"
                        :placeholder="displayFormat"
                        :value="inputValue"
                        :disabled="readOnly"
                        autocomplete="off"
                        @input="handleInput"
                        @blur="handleBlur"
                        @keydown.up.prevent="increase"
                        @keydown.down.prevent="decrease"
                        ref="input"
                    >
                </template>
                <template v-else>
                    <template v-if="hasDate">
                        <input
                            :id="id"
                            class="form-control SharpDate__date-input"
                            :value="dateInputValue"
                            type="date"
                            autocomplete="off"
                            @input="handleDateInput"
                        >
                    </template>
                    <template v-if="hasTime">
                        <input
                            :id="!hasDate ? id : null"
                            class="form-control SharpDate__date-input"
                            :value="timeInputValue"
                            type="time"
                            autocomplete="off"
                            @input="handleTimeInput"
                        >
                    </template>
                </template>

                <template v-if="hasClearButton">
                    <ClearButton @click="clear" ref="clearButton" />
                </template>
            </div>
        </div>
        <template v-if="displayFormat">
            <b-popover
                :target="popoverTarget"
                :show.sync="showPicker"
                :boundary="popoverBoundary"
                no-fade
                triggers="focus"
                placement="bottom"
            >
                <div class="SharpDate__picker position-static">
                    <template v-if="hasDate">
                        <DatePicker
                            class="SharpDate__date"
                            :language="language"
                            :monday-first="mondayFirst"
                            inline
                            :value="dateObject"
                            @selected="handleDateSelect"
                            ref="datepicker"
                        />
                    </template>
                    <template v-if="hasTime">
                        <TimePicker
                            class="SharpDate__time"
                            :value="timeObject"
                            :active="showPicker"
                            :format="displayFormat"
                            :minute-interval="stepTime"
                            :min="minTime" :max="maxTime"
                            @change="handleTimeSelect"
                            ref="timepicker"
                        />
                    </template>
                </div>
            </b-popover>
        </template>
    </div>
</template>

<script>
    import moment from 'moment';
    import { BPopover } from 'bootstrap-vue';

    import { lang } from 'sharp';
    import { Localization } from 'sharp/mixins';
    import { ClearButton } from "sharp-ui";
    import DatePicker from './Datepicker';
    import TimePicker from './Timepicker';


    export default {
        name:'SharpDate',
        components: {
            DatePicker,
            TimePicker,
            BPopover,
            ClearButton,
        },

        inject:['$field'],

        mixins: [Localization],

        props: {
            id: String,
            value: {
                type: [Date, String]
            },
            hasDate: {
                type: Boolean,
                default: true
            },
            hasTime: {
                type: Boolean,
                default: false
            },
            displayFormat: String,
            mondayFirst: Boolean,
            stepTime: {
                type: Number,
                default: 30,
            },
            minTime: String,
            maxTime: String,

            readOnly: Boolean
        },
        data() {
            return {
                showPicker: false,
                localInputValue: null
            }
        },
        computed: {
            format() {
                return this.hasTime && !this.hasDate
                    ? 'HH:mm'
                    : null;
            },
            dateObject() {
                return this.value
                    ? moment(this.value, this.format).toDate()
                    : null;
            },
            timeObject() {
                return this.value
                    ? {
                        HH: moment(this.value, this.format).format('HH'),
                        mm: moment(this.value, this.format).format('mm')
                    }
                    : null;
            },
            inputValue() {
                if(typeof this.localInputValue === 'string') {
                    return this.localInputValue;
                }
                return this.value
                    ? moment(this.value, this.format).format(this.displayFormat)
                    : '';
            },
            popoverBoundary() {
                return document.querySelector('[data-popover-boundary]');
            },
            timeInputValue() {
                return this.value
                    ? moment(this.value).format('HH:mm')
                    : '';
            },
            dateInputValue() {
                return this.value
                    ? moment(this.value).format('YYYY-MM-DD')
                    : '';
            },
            hasClearButton() {
                return !!this.displayFormat && !!this.value;
            },
        },
        methods: {
            popoverTarget() {
                return this.$refs.inputGroup;
            },

            getMoment() {
                return this.value
                    ? moment(this.value, this.format)
                    : moment();
            },
            handleDateInput(e) {
                if(!e.target.valueAsDate) {
                    return;
                }
                // todo
                this.$emit('input', date);
            },
            handleTimeInput(e) {
                if(!this.hasDate) {
                    return e.target.value;
                }
                if(!e.target.valueAsDate) {
                    return;
                }
                // todo
                this.$emit('input', date);
            },
            handleDateSelect(date) {
                let newMoment = this.getMoment();
                newMoment.set({
                    year: date.getFullYear(),
                    month: date.getMonth(),
                    date: date.getDate()
                });
                this.$emit('input', newMoment.toDate());
            },
            handleTimeSelect({ data }) {
                let newMoment = this.getMoment();
                newMoment.set({
                    hour: data.HH,
                    minute: data.mm,
                    second: data.ss,
                });
                if(this.getMoment().format('HH:mm') === newMoment.format('HH:mm')) {
                    return;
                }
                this.$emit('input', newMoment.toDate());
            },
            handleInput(e) {
                let m = moment(e.target.value, this.displayFormat, true);
                this.localInputValue = e.target.value;
                this.showPicker = false;
                if(!m.isValid()) {
                    this.$field.$emit('error', `${lang('form.date.validation_error.format')} (${this.displayFormat})`);
                }
                else {
                    this.rollback();
                    this.$emit('input', m.toDate());
                }
            },
            handlePrependButtonPointerDown(e) {
                const button = e.target.closest('button');
                this.toggleOnClick = button === document.activeElement;
            },
            handlePrependButtonClicked() {
                if(this.toggleOnClick) {
                    this.showPicker = !this.showPicker;
                }
            },
            increase(e) {
                this.translate(e.target, 1)
            },
            decrease(e) {
                this.translate(e.target, -1)
            },
            async translate(input, amount) {
                let selection = this.changeOnArrowPressed(input.selectionStart, amount);

                if(selection)  {
                    await this.$nextTick();
                    input.setSelectionRange(selection.start, selection.end);
                }
            },
            add(amount, unit) {
                const date = this.getMoment();
                date.add(amount, unit)
                this.$emit('input', date.toDate());
            },
            nearestMinutesDist(dir) { //dir = 1 or -1
                let curM = this.getMoment().minutes(); //current minutes
                if(curM%this.stepTime === 0) {
                    return dir*this.stepTime;
                }
                let fn = dir<0 ? 'floor' : 'ceil';
                return this.stepTime * Math[fn](curM/this.stepTime) - curM;
            },
            updateMoment(ch, amount) {
                switch(ch) {
                    case 'H': this.add(amount,'hours'); break;
                    case 'm': this.add(this.nearestMinutesDist(amount),'minutes'); break;
                    case 's': this.add(amount,'seconds'); break;
                    case 'Y': this.add(amount,'years'); break;
                    case 'M': this.add(amount,'months'); break;
                    case 'D': this.add(amount,'days'); break;
                    default: return false;
                }
                return true;
            },
            changeOnArrowPressed(pos,amount) {
                let lookupPos = pos;
                if(!this.updateMoment(this.displayFormat[lookupPos],amount) && pos) {
                    lookupPos--;
                    if(!this.updateMoment(this.displayFormat[lookupPos],amount))
                        return null;
                }
                let ch = this.displayFormat[lookupPos];
                return {
                    start: this.displayFormat.indexOf(ch),
                    end: this.displayFormat.lastIndexOf(ch)+1
                };
            },

            rollback() {
                this.$field.$emit('clear');
                this.localInputValue = null;
            },

            clear() {
                this.rollback();
                this.$emit('input', null);
                this.$refs.input.focus();
            },

            handleBlur() {
                this.rollback();
            }
        },
    }
</script>
