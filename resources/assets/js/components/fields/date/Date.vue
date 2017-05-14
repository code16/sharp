<template>
    <div class="SharpDate">
        <input class="form-control" :value="inputValue" @input="handleInput" @focus="showPicker=true"
               @blur="handleBlur" @keydown.up.prevent="increase" @keydown.down.prevent="decrease">
        <div class="SharpDate__picker" v-show="showPicker">
            <date-picker v-if="hasDate"
                        class="SharpDate__picker-inner SharpDate__date"
                        language="fr"
                        inline monday-first
                        :value="dateObject"
                        @selected="handleDateSelect">
            </date-picker>
            <sharp-time-picker v-if="hasTime"
                                class=" SharpDate__time"
                                :value="timeObject" 
                                :active="showPicker"
                                :format="displayFormat"
                                @change="handleTimeSelect">
            </sharp-time-picker>
        </div>
    </div>
</template>

<script>
    import DatePicker from 'vuejs-datepicker';
    import SharpTimePicker from './Timepicker';

    import moment from 'moment';

    export default {
        name:'SharpDate',
        components: {
            DatePicker,
            SharpTimePicker
        },

        inject:['$field'],

        props: {
            value: [Object, String],

            hasDate: {
                type:Boolean,
                default:true
            },
            hasTime: {
                type:Boolean,
                default:false
            },
            minDate: String,
            maxDate: String,
            displayFormat: {
                type: String,
                default:'DD/MM/YYYY HH:mm:ss'
            }
        },
        data() {
            return {
                showPicker:false,
                haveError:false
            }
        },
        computed: {
            moment() {
                return moment(this.value);
            },
            dateObject() {
                return this.moment.toDate();
            },
            timeObject() {
                return {
                    HH: this.moment.format('HH'),
                    mm: this.moment.format('mm'),
                    ss: this.moment.format('ss')
                }
            },
            inputValue() {
                return this.moment.format(this.displayFormat);
            },
            minDateFormatted() { return moment(this.minDate).format('DD MMM YYYY') },
            maxDateFormatted() { return moment(this.maxDate).format('DD MMM YYYY') }
        },
        methods: {
            handleDateSelect(date) {
                this.moment.set({
                    year:date.getFullYear(),
                    month:date.getMonth(),
                    date:date.getDate()
                });
                let boundsError = this.dateOutOfBounds(this.moment);
                if(boundsError)
                    this.$field.$emit('alert','dateOutOfBounds','error',boundsError);
                else
                    this.$emit('input', this.moment);
            },
            handleTimeSelect({ data }) {
                this.moment.set({
                    hour:data.HH,
                    minute:data.mm,
                    second:data.ss,
                });
                let boundsError = false;
                if(boundsError)
                    this.$field.$emit('alert','timeOutOfBounds','error',boundsError);
                else
                    this.$emit('input', this.moment);
            },
            handleInput(e) {
                let m = moment(e.target.value, this.displayFormat, true);
                if(!m.isValid()) {
                    this.$field.$emit('error', "Format de l'heure invalide");
                }
                else {
                    this.$field.$emit('ok');
                    this.$emit('input', m);
                }
            },
            dateOutOfBounds(m) {
                return !(!this.minDate || m.isAfter(this.minDate)) ? 
                            `La date doit être supérieur au ${this.minDateFormatted}` :
                       !(!this.maxDate || m.isBefore(this.maxDate)) ? 
                            `La date doit être inférieur au ${this.maxDateFormatted}` :
                       false;
            },
            handleBlur() {
                this.$field.$emit('clear');
            },
            increase(e) {
                let pos = e.target.selectionStart;
                let s=this.changeOnArrowPressed(pos, 1);
                if(s)
                    this.$nextTick(_=>e.target.setSelectionRange(s.selectStart,s.selectEnd));
            },
            decrease(e) {
                let pos = e.target.selectionStart;
                let s=this.changeOnArrowPressed(pos, -1);
                if(s)
                    this.$nextTick(_=>e.target.setSelectionRange(s.selectStart,s.selectEnd));
            },
            add(amount, key) {
                this.moment.add.apply(this.moment,arguments);
                let boundsError;
                if(['years','months','days'].includes(key) && (boundsError=this.dateOutOfBounds(this.moment))) {
                    this.$field.$emit('alert','dateOutOfBounds','error',boundsError);
                }
                else this.$field.$emit('alert-clear','dateOutOfBounds');
                this.$emit('input',this.moment);
            },
            updateMoment(ch, amount) {
                //console.log('add',ch,amount);
                switch(ch) {
                    case 'H': this.add(amount,'hours'); break;
                    case 'm': this.add(amount,'minutes'); break;
                    case 's': this.add(amount,'seconds'); break;
                    case 'Y': this.add(amount,'years'); break;
                    case 'M': this.add(amount,'months'); break;
                    case 'D': this.add(amount,'days'); break;
                    default: return false;
                }
                return true;
            },
            changeOnArrowPressed(pos,amount) {
                let lookupPos=pos;
                if(!this.updateMoment(this.displayFormat[pos],amount) && pos) {
                    lookupPos=pos-1;
                    if(!this.updateMoment(this.displayFormat[pos-1],amount))
                        return null;
                }
                let ch=this.displayFormat[lookupPos];
                return {
                    selectStart:this.displayFormat.indexOf(ch),
                    selectEnd:this.displayFormat.lastIndexOf(ch)+1
                };
            }
        },
        mounted() {
            document.addEventListener('click', (e) => {
                if (!this.$el.contains(e.target)) {
                    this.showPicker = false;
                }
            }, false);
        }
    }
</script>