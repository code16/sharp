<template>
    <span class="time-picker">
        <div class="dropdown">
            <div class="select-list">
                <ul class="hours" ref="hours">
                    <li class="hint" v-text="hourType"></li>
                    <li v-for="hr in hours" v-text="hr" :class="{active: hour === hr}" @click.stop="select2('hour', hr)"></li>
                </ul>
                <ul class="minutes" ref="minutes">
                    <li class="hint" v-text="minuteType"></li>
                    <li v-for="m in minutes" v-text="m" :class="{active: minute === m}" @click.stop="select2('minute', m)"></li>
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

    export default {
        name:'SharpTimePicker',
        extends:TimePicker,
        props: {
            active:Boolean
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
            }
        },
        mounted() {
            console.log(this);
        }
    }
</script>