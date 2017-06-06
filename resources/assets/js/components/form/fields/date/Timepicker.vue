<template>
    <span class="time-picker">
        <div class="dropdown">
            <div class="select-list">
                <ul class="hours" ref="hours">
                    <li class="hint" v-text="hourType"></li>
                    <li v-for="hr in croppedHours" v-text="hr" :class="{active: hour === hr}" @click.stop="select2('hour', hr)"></li>
                </ul>
                <ul class="minutes" ref="minutes">
                    <li class="hint" v-text="minuteType"></li>
                    <li v-for="m in croppedMinutes" v-text="m" :class="{active: minute === m}" @click.stop="select2('minute', m)"></li>
                </ul>
                <ul class="seconds" ref="seconds" v-if="secondType">
                    <li class="hint" v-text="secondType"></li>
                    <li v-for="s in seconds" v-text="s" :class="{active: second === s}" @click.stop="select2('second', s)"></li>
                </ul>
                <ul class="apms" v-if="apmType">
                    <li class="hint" v-text="apmType"></li>
                    <li v-for="a in apms" v-text="a" :class="{active: apm === a}" @click.stop="select2('apm', a)"></li>
                </ul>
            </div>
        </div>
    </span>
</template>

<script>
    import TimePicker from 'vue2-timepicker';
    import moment from 'moment';

    const {
        methods: {
            renderFormat
        }
    } = TimePicker;

    export default {
        name:'SharpTimePicker',
        mixins:[TimePicker],
        props: {
            active:Boolean,
            min:String,
            max:String,
            minMaxFormat: {
                type:String,
                default:'HH:mm'
            }
        },
        data() {
            return {
                showDropdown:true,
                isSelection:false
            }
        },
        watch: {
            minute() { this.$nextTick(_=>this.updateScroll('minutes')); },
            second() { this.$nextTick(_=>this.updateScroll('seconds')); },
            hour() { this.$nextTick(_=>this.updateScroll('hours')); },
            active(a) { a && ['minutes','seconds','hours'].forEach(ref=>this.updateScroll(ref)); },
        },
        computed: {
            minMoment() {
                let m = this.min ? moment(this.min,this.minMaxFormat) : moment('0:0',this.minMaxFormat);
                return {
                    minutes: m.minutes(),
                    hours: m.hours()
                }
            },
            maxMoment() {
                let m = this.max ? moment(this.max,this.minMaxFormat) : moment('23:59',this.minMaxFormat);
                return {
                    minutes:m.minutes(),
                    hours:m.hours()
                }
            },
            croppedHours() {
                return this.hours.filter(h => {
                    h = parseInt(h);
                    return h>=this.minMoment.hours && h<=this.maxMoment.hours;
                });
            },
            croppedMinutes() {
                let hour = parseInt(this.hour);
                if(hour===this.minMoment.hours) {
                    return this.minutes.filter(m => {
                        m = parseInt(m);
                        return m>=this.minMoment.minutes;
                    });
                }
                else if(hour===this.maxMoment.hours) {
                    return this.minutes.filter(m => {
                        m = parseInt(m);
                        return m<=this.minMoment.minutes;
                    });
                }
                return this.minutes;
            }
        },
        methods: {
            select2(type, value) {
               this.select(type, value);
               this.isSelection = true;
            },
            updateScroll(listRef) {
                if(this.isSelection)
                    return this.isSelection=false;

                let list = this.$refs[listRef];
                if(list) {
                    let activeItem = list.querySelector('.active');
                    if(activeItem)
                        list.scrollTop = activeItem.offsetTop-list.offsetHeight/2.+activeItem.offsetHeight/2.;
                }
            },
            renderFormat() {
                renderFormat.apply(this, arguments);
            },
        },
        mounted() {
            //console.log(this);
        }
    }
</script>