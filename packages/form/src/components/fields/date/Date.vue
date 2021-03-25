<template>
    <div class="SharpDate" :class="{'SharpDate--open':showPicker}">
        <div class="SharpDate__input-wrapper">
            <input
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
            <template v-if="value">
                <ClearButton @click="clear" ref="clearButton" />
            </template>
        </div>
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
    </div>
</template>

<script>
    import moment from 'moment';
    import { BPopover } from 'bootstrap-vue';

    import { lang } from 'sharp';
    import { Focusable, Localization } from 'sharp/mixins';
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

        mixins: [ Focusable, Localization ],

        props: {
            value: {
                type:[Date, String]
            },
            hasDate: {
                type:Boolean,
                default:true
            },
            hasTime: {
                type:Boolean,
                default:false
            },
            displayFormat: {
                type: String,
                default:'DD/MM/YYYY HH:mm'
            },
            mondayFirst: Boolean,
            stepTime: {
                type:Number,
                default:30
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
        },
        methods: {
            popoverTarget() {
                return this.$refs.input;
            },

            getMoment() {
                return this.value
                    ? moment(this.value, this.format)
                    : moment();
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
                if(!m.isValid()) {
                    this.$field.$emit('error', `${lang('form.date.validation_error.format')} (${this.displayFormat})`);
                    this.showPicker = false;
                }
                else {
                    this.rollback();
                    this.$emit('input', m.toDate());
                    this.showPicker = true;
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
        mounted() {
            this.setFocusable(this.$refs.input);
        }
    }
</script>
