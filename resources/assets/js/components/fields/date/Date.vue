<template>
    <div class="SharpDate">
        <date-picker class="SharpDate__picker SharpDate__picker--date"
                     input-class="form-control"
                     language="fr"
                     ref="datepicker">
        </date-picker>
        <time-picker class="SharpDate__picker SharpDate__picker--time"
                     ref="timepicker">
        </time-picker>
    </div>
</template>

<script>
    import DatePicker from 'vuejs-datepicker';
    import TimePicker from 'vue2-timepicker';

    //import moment from 'moment';

    export default {
        name:'SharpDate',
        components: {
            DatePicker,
            TimePicker
        },
        props: {
            value: String
        },
        methods: {
            onDatepickerOpen() {
                console.log('datepicker open');
            },
            onDatepickerClose() {
                console.log('datepicker close');
            },
            onTimepickerOpen() {
                console.log('timepicker open');
                this.$refs.datepicker.close();
            },
            onTimepickerClose() {
                console.log('timepicker close');
            },
        },
        mounted() {
            this.$refs.datepicker.$watch('showDayView',val=>{
                val ? this.onDatepickerOpen() : this.onDatepickerClose();
            });
            this.$refs.timepicker.$watch('showDropdown',val=>{
                val ? this.onTimepickerOpen() : this.onTimepickerClose();
            });
            console.log(this);
        }
    }
</script>