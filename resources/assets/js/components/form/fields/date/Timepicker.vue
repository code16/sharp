<template>
    <span class="time-picker">
        <header>
            <span class="hint" v-text="hourType"></span>
            <span class="hint" v-text="minuteType"></span>
        </header>
        <div class="dropdown">
            <div class="select-list">
                <ul class="hours" ref="hours">
                    <li v-for="hr in croppedHours" v-text="hr" :class="{active: value && hour === hr}" @click.stop="select2('hour', hr)"></li>
                </ul>
                <ul class="minutes" ref="minutes">
                    <li v-for="m in croppedMinutes" v-text="m" :class="{active: value && minute === m}" @click.stop="select2('minute', m)"></li>
                </ul>
                <!--<ul class="seconds" ref="seconds" v-if="secondType">-->
                    <!--<li class="hint" v-text="secondType"></li>-->
                    <!--<li v-for="s in seconds" v-text="s" :class="{active: second === s}" @click.stop="select2('second', s)"></li>-->
                <!--</ul>-->
                <!--<ul class="apms" v-if="apmType">-->
                    <!--<li class="hint" v-text="apmType"></li>-->
                    <!--<li v-for="a in apms" v-text="a" :class="{active: apm === a}" @click.stop="select2('apm', a)"></li>-->
                <!--</ul>-->
            </div>
        </div>
    </span>
</template>

<script>
    import TimePicker from 'vue2-timepicker';

    import moment from 'moment';

    import { AutoScroll } from '../../../../mixins';

    const {
        methods: {
            renderFormat
        }
    } = TimePicker;

    export default {
        name: 'SharpTimePicker',
        mixins: [TimePicker, AutoScroll],
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
            hour() { this.$nextTick(_=>this.updateScroll('hours')); },
            active(a) { a && ['minutes','hours'].forEach(ref=>this.updateScroll(ref)); },
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
            },

            autoScrollOptions() {
                return listRef => {
                    if(this.isSelection)
                        return this.isSelection=false;
                    return {
                        list: this.$refs[listRef],
                        item: () => this.$refs[listRef].querySelector('.active')
                    };
                }
            }
        },
        methods: {
            select2(type, value) {
               this.select(type, value);
               this.isSelection = true;
            },
            renderFormat() {
                renderFormat.apply(this, arguments);
            },
        }
    }
</script>