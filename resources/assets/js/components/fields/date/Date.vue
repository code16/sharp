<template>
    <div class="SharpDate" :class="rootClasses">
        <date-picker v-if="hasDate"
                     class="SharpDate__picker"
                     :class="pickerClass('date', !hasTime)"
                     language="fr"
                     :value="dateObject"
                     :format="datepickerFormat"
                     @selected="updateDate"
                     @closed="onDatepickerClose"
                     @opened="onDatepickerOpen"
                     ref="datepicker">
        </date-picker>
        <time-picker v-if="hasTime"
                     class="SharpDate__picker"
                     :class="pickerClass('time', !hasDate)"
                     :value="timeObject" @change="updateTime"
                     ref="timepicker">
        </time-picker>
    </div>
</template>

<script>
    import DatePicker from 'vuejs-datepicker';
    import TimePicker from 'vue2-timepicker';

    import moment from 'moment';

    export default {
        name:'SharpDate',
        components: {
            DatePicker,
            TimePicker
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

            displayFormat: {
                type: String,
                default:'DD/MM/YYYY'
            }
        },
        data() {
            return {
                dateActive:false,
                dateFocused:false,
                timeActive:false,
                timeFocused:false,
            }
        },
        computed: {
            rootClasses() {
                return {
                    'SharpDate--date-active':this.dateActive,
                    'SharpDate--time-active':this.timeActive,
                    'SharpDate--date-focused':this.dateFocused,
                    'SharpDate--time-focused':this.timeFocused,
                }
            },
            moment() {
                return moment(this.value);
            },
            dateObject() {
                return this.moment.toDate();
            },
            timeObject() {
                return {
                    HH: this.moment.hours(),
                    mm: this.moment.minutes(),
                    ss: this.moment.seconds()
                }
            },
            datepickerFormat() {
                return this.displayFormat.replace(/Y|D/g,m=>({'Y':'y','D':'d'}[m]));
            }
        },
        methods: {
            /// Datepicker events
            onDatepickerOpen() {
                this.dateActive=true;
                console.log('datepicker open');
            },
            onDatepickerClose() {
                this.dateActive=false;
                console.log('datepicker close');
            },

            /// Timepicker events
            onTimepickerOpen() {
                this.timeActive=true;
                console.log('timepicker open');
                this.$refs.datepicker.close();
            },
            onTimepickerClose() {
                this.timeActive=false;
                console.log('timepicker close');
            },
            onDateInput(val) {
                console.log(val);
                let m=moment(val, this.momentValidationFormat);
                if(!m.isValid()) {
                    this.$field.$emit('error', 'Format de la date invalide');
                }
                else
                    this.$field.$emit('ok');
            },
            onTimeInput(val) {
                if(!moment(val, this.displayFormat).isValid()) {
                    this.$field.$emit('error', "Format de l'heure invalide");
                }
                else
                    this.$field.$emit('ok');
            },
            updateDate(date) {
                this.moment.set({
                    year:date.getFullYear(),
                    month:date.getMonth(),
                    date:date.getDate()
                });
                this.$emit('input', this.moment);
            },
            updateTime({ data }) {
                this.moment.set({
                    hour:data.HH,
                    minute:data.mm,
                    second:data.ss,
                });
                this.$emit('input', this.moment);
            },
            pickerClass(label,isAlone) {
                return [
                    `SharpDate__picker__${label}`, {
                        [`SharpDate__picker__${label}--alone`]:isAlone
                    }
                ]
            }
        },
        mounted() {
            if(this.$refs.timepicker) {
                this.$refs.timepicker.$watch('showDropdown',val=>{
                    val ? this.onTimepickerOpen() : this.onTimepickerClose();
                });

                let $timeInput = this.$refs.timepicker.$el.querySelector('input');

                $timeInput.addEventListener('focus', _=>this.timeFocused=true);
                $timeInput.addEventListener('blur', _=>this.timeFocused=false);

                $timeInput.addEventListener('input', e=>this.onTimeInput(e.target.value));

                $timeInput.readOnly = false;

                document.addEventListener('click', (e) => {
                    if (!this.$refs.timepicker.$el.contains(e.target)) {
                        this.$refs.timepicker.showDropdown = false;
                    }
                }, false);
            }

            if(this.$refs.datepicker) {
                let $dateInput = this.$refs.datepicker.$el.querySelector('input');
                $dateInput.addEventListener('focus', _=>this.dateFocused=true);
                $dateInput.addEventListener('blur', _=>this.dateFocused=false);

                $dateInput.addEventListener('input', e=>this.onDateInput(e.target.value));

                $dateInput.readOnly = false;
            }

            console.log(this);
        }
    }
</script>