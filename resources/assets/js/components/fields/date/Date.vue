<template>
    <div class="SharpDate" :class="rootClasses">
        <date-picker v-if="hasDate"
                     class="SharpDate__picker"
                     :class="pickerClass('date', !hasTime)"
                     language="fr"
                     :value="dateObject"
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


    export default {
        name:'SharpDate',
        components: {
            DatePicker,
            TimePicker
        },
        props: {
            value: String,

            hasDate: {
                type:Boolean,
                default:true
            },
            hasTime: {
                type:Boolean,
                default:false
            }
        },
        data() {
            return {
                dateActive:false, dateFocused:false,
                timeActive:false, timeFocused:false,
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
            str() {
                let splitted = this.value.split(' ');
                return {
                    date: splitted[0] || '',
                    time: splitted[1] || ''
                }
            },
            dateObject() {
                let dateValues = this.str.date.split('-');
                if(dateValues.length === 3)
                    return new Date(dateValues[0], dateValues[1], dateValues[2]);
                return null;
            },
            timeObject() {
                let timeValues = this.str.time.split(':');
                //debugger
                if(timeValues.length === 3)
                    return { HH:timeValues[0], mm:timeValues[1], ss:timeValues[2] };
                return null;
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

            updateDate(date) {
                let d=`${date.getYear()}-${date.getMonth()}-${date.getDay()}`;
                if(this.str.time)
                    d+=this.str.time;
                this.$emit('input', d);
            },
            updateTime({ data }) {
                let d='';
                if(this.str.date)
                    d+=`${this.str.date} `;
                d+=`${data.HH}:${data.mm}:${data.ss}`;
                this.$emit('input', d);
            },
            pickerClass(label,isAlone) {
                return [
                    `Sharp__picker__${label}`, {
                        [`Sharp__picker__${label}--alone`]:isAlone
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
                $dateInput.addEventListener('blur', _=>this.dateFocused = false);
                $dateInput.readOnly = false;
            }

            console.log(this);
        }
    }
</script>