<template>
    <div class="SharpDate">
        <div class="SharpDate__input-wrapper position-relative">
            <DatePicker
                :value="pickerValue"
                :mode="mode"
                :valid-hours="validHours"
                :minute-increment="stepTime"
                :monday-first="mondayFirst"
                :update-on-input="false"
                @input="handleDateChanged"
                v-slot="{ inputEvents, togglePopover }"
            >
                <div class="input-group" :class="{ 'input-group--clearable': hasClearButton }">
                    <button class="input-group-text btn" @click="handlePrependButtonClicked">
                        <svg class="align-middle" width="1.25em" height="1.25em" viewBox="0 0 32 32" style="fill:currentColor">
                            <path d="M26,4h-4V2h-2v2h-8V2h-2v2H6C4.9,4,4,4.9,4,6v20c0,1.1,0.9,2,2,2h20c1.1,0,2-0.9,2-2V6C28,4.9,27.1,4,26,4z M26,26H6V12h20  V26z M26,10H6V6h4v2h2V6h8v2h2V6h4V10z"/>
                        </svg>
                    </button>

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
                        v-on="inputEvents"
                        ref="input"
                    >

                    <template v-if="hasClearButton">
                        <ClearButton @click="clear" ref="clearButton" />
                    </template>
                </div>
            </DatePicker>
        </div>
    </div>
</template>

<script>
    import moment from 'moment';
    import 'moment/locale/fr';
    import { lang } from 'sharp';
    import { Localization } from 'sharp/mixins';
    import { ClearButton } from "sharp-ui";
    import DatePicker from './DatePicker';

    moment.locale(
        moment.locales().includes(document.documentElement.lang)
            ? document.documentElement.lang
            : 'en'
    );

    export default {
        name:'SharpDate',
        components: {
            DatePicker,
            ClearButton,
        },

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
                localInputValue: null,
            }
        },
        computed: {
            format() {
                return this.hasTime && !this.hasDate
                    ? 'HH:mm'
                    : null;
            },
            mode() {
                if(this.hasDate && this.hasTime) {
                    return 'dateTime';
                }
                if(this.hasTime) {
                    return 'time';
                }
                return 'date';
            },
            validHours() {
                return {
                    min: this.minTime ? parseInt(this.minTime) : null,
                    max: this.maxTime ? parseInt(this.maxTime) : null,
                }
            },
            pickerValue() {
                return this.value
                    ? moment(this.value, this.format).toDate()
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
            hasClearButton() {
                return !!this.value;
            },
        },
        methods: {
            getMoment() {
                return this.value
                    ? moment(this.value, this.format)
                    : moment();
            },
            handleDateChanged(date) {
                this.$emit('input', date);
            },
            handleInput(e) {
                const m = moment(e.target.value, this.displayFormat, true);
                this.localInputValue = e.target.value;
                if(!m.isValid()) {
                    this.$emit('error', `${lang('form.date.validation_error.format')} (${this.displayFormat})`);
                }
                else {
                    this.rollback();
                    this.$emit('input', m.toDate());
                    this.updatePopover();
                }
            },
            handlePrependButtonClicked() {
                setTimeout(() => this.$refs.input.focus());
            },
            handleBlur() {
                if(this.localInputValue) {
                    this.rollback();
                }
            },
            updatePopover() {
                this.$refs.input.dispatchEvent(new Event('change', { bubbles:true }));
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
                    this.updatePopover();
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
                this.$emit('clear');
                this.localInputValue = null;
            },

            clear() {
                this.rollback();
                this.$emit('input', null);
                setTimeout(() => this.$refs.input.focus());
            },
        },
    }
</script>
