<script>
    import DatePicker from 'vuejs-datepicker';
    export default {
        name:'SharpDatepicker',
        extends:DatePicker,

        methods: {
            init() {
                if (this.value) {
                    this.setValue(this.value)
                }
                this.showDayCalendar();
            }
        },
    }
</script>