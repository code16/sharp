<template>
    <span class="time-picker">
        <header>
            <span class="hint" v-text="hourType"></span>
            <span class="hint" v-text="minuteType"></span>
        </header>
        <div class="dropdown">
            <div class="select-list">
                <ul class="hours" ref="hours">
                    <template v-for="hr in croppedHours">
                        <li :class="{ 'active': value && hour === hr }" @click.stop="select2('hour', hr)">{{ hr }}</li>
                    </template>
                </ul>
                <ul class="minutes" ref="minutes">
                    <template v-for="m in croppedMinutes">
                        <li :class="{ 'active': value && minute === m }" @click.stop="select2('minute', m)">{{ m }}</li>
                    </template>
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

    const {
        methods: {
            renderFormat
        }
    } = TimePicker;

    export default {
        name: 'SharpTimePicker',
        mixins: [TimePicker],
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
            minute: 'layout',
            hour: 'layout',
            active(active) {
                if(active) {
                    this.layout(false);
                }
            },
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
                        return m<=this.maxMoment.minutes;
                    });
                }
                return this.minutes;
            },
        },
        methods: {
            select2(type, value) {
               this.select(type, value);
               this.isSelection = true;
            },
            renderFormat() {
                renderFormat.apply(this, arguments);
            },
            async layout(smooth = true) {
                const behavior = smooth ? 'smooth' : 'auto';
                await this.$nextTick();
                this.$refs.minutes.querySelector('.active')?.scrollIntoView({ block:'center', behavior });
                this.$refs.hours.querySelector('.active')?.scrollIntoView({ block:'center', behavior });
            },
        }
    }
</script>