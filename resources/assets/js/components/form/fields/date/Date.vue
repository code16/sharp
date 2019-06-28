<template>
    <div class="SharpDate" :class="{'SharpDate--open':showPicker}">
        <div class="SharpDate__input-wrapper">
            <input class="SharpDate__input"
                   :placeholder="displayFormat"
                   :value="inputValue"
                   :disabled="readOnly"
                   @input="handleInput"
                   @blur="handleBlur"
                   @keydown.up.prevent="increase"
                   @keydown.down.prevent="decrease"
                   ref="input">
            <button class="SharpDate__clear-button" type="button" @click="clear()" ref="clearButton">
                <svg class="SharpDate__clear-button-icon"
                     aria-label="close" width="10" height="10" viewBox="0 0 10 10" fill-rule="evenodd">
                    <path d="M9.8 8.6L8.4 10 5 6.4 1.4 10 0 8.6 3.6 5 .1 1.4 1.5 0 5 3.6 8.6 0 10 1.4 6.4 5z"></path>
                </svg>
            </button>
        </div>
        <b-popover :target="()=>$refs.input" :show.sync="showPicker" triggers="focus" placement="bottom">
            <div class="SharpDate__picker position-static">
                <template v-if="hasDate">
                    <sharp-date-picker
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
                    <sharp-time-picker
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
    import SharpDatePicker from './Datepicker';
    import SharpTimePicker from './Timepicker';

    import { Focusable, Localization } from '../../../../mixins';
    import { lang } from '../../../../mixins/Localization';

    import moment from 'moment';
    import bPopover from 'bootstrap-vue/es/components/popover/popover';

    export default {
        name:'SharpDate',
        components: {
            SharpDatePicker,
            SharpTimePicker,
            bPopover,
        },

        inject:['$field'],

        mixins: [ Focusable, Localization ],

        props: {
            value: {
                type:[Object, String]
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
            moment() {
                return this.value && moment(this.value, this.hasTime && !this.hasDate ? 'HH:mm' : null);
            },
            dateObject() {
                return this.moment ? this.moment.toDate() : null;
            },
            timeObject() {
                return this.moment ? {
                    HH: this.moment.format('HH'),
                    mm: this.moment.format('mm')
                } : null;
            },
            inputValue() {
                return typeof this.localInputValue === 'string'
                    ? this.localInputValue
                    : (this.moment ? this.moment.format(this.displayFormat) : '');
            },
        },
        methods: {
            getMoment() {
                return this.moment || moment();
            },

            handleDateSelect(date) {
                let newMoment = this.getMoment();
                newMoment.set({
                    year:date.getFullYear(),
                    month:date.getMonth(),
                    date:date.getDate()
                });
                this.$emit('input', newMoment);
            },
            handleTimeSelect({ data }) {
                let newMoment = this.getMoment();
                newMoment.set({
                    hour:data.HH,
                    minute:data.mm,
                    second:data.ss,
                });
                this.$emit('input', newMoment);
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
                    this.$emit('input', m);
                    this.showPicker = true;
                }
            },

            increase(e) {
                this.translate(e.target, 1)
            },
            decrease(e) {
                this.translate(e.target, -1)
            },
            translate(input, amount) {
                let selection = this.changeOnArrowPressed(input.selectionStart, amount);

                if(selection)  {
                    this.$nextTick(_=>input.setSelectionRange(selection.start,selection.end));
                }
            },
            add(amount, key) {
                this.moment.add.apply(this.moment,arguments);
                this.$emit('input',this.moment);
            },
            nearestMinutesDist(dir) { //dir = 1 or -1
                let curM = this.moment.minutes(); //current minutes
                if(curM%this.stepTime === 0) {
                    return dir*this.stepTime;
                }
                let fn = dir<0 ? 'floor' : 'ceil';
                return this.stepTime * Math[fn](curM/this.stepTime) - curM;
            },
            updateMoment(ch, amount) {
                //console.log('add',ch,amount);
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
                let lookupPos=  pos;
                if(!this.updateMoment(this.displayFormat[lookupPos],amount) && pos) {
                    lookupPos--;
                    if(!this.updateMoment(this.displayFormat[lookupPos],amount))
                        return null;
                }
                let ch=this.displayFormat[lookupPos];
                return {
                    start:this.displayFormat.indexOf(ch),
                    end:this.displayFormat.lastIndexOf(ch)+1
                };
            },

            rollback() {
                this.$field.$emit('clear');
                this.localInputValue = null;
            },

            clear() {
                this.rollback();
                this.$emit('input', null);
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