<template>
    <div class="SharpDate__datepicker" :class="wrapperClass">
        <input
                :type="inline ? 'hidden' : 'text'"
                :class="inputClass"
                :name="name"
                :id="id"
                @click="showCalendar()"
                :value="formattedValue"
                :placeholder="placeholder"
                :clear-button="clearButton"
                :disabled="disabledPicker"
                :required="required"
                readonly>
        <i class="vdp-datepicker__clear-button" v-if="clearButton && selectedDate" @click="clearDate()">&times;</i>
        <!-- Day View -->
        <div class="SharpDate__calendar" v-show="showDayView" :class="{open:showDayView}" v-bind:style="calendarStyle">
            <header>
                <span
                        @click="previousMonth"
                        class="prev"
                        v-bind:class="{ 'disabled' : previousMonthDisabled(pageTimestamp) }">
                    <svg width="8" height="12" viewBox="0 0 8 12" fill-rule="evenodd">
                        <path d="M7.5 10.6L2.8 6l4.7-4.6L6.1 0 0 6l6.1 6z"></path>
                    </svg>
                </span>
                <span @click="showMonthCalendar" class="up">
                    <span class="SharpDate__cur-month">{{ currMonthName }}</span>
                    <span class="SharpDate__cur-year">{{ currYear }}</span>
                </span>
                <span
                        @click="nextMonth"
                        class="next"
                        v-bind:class="{ 'disabled' : nextMonthDisabled(pageTimestamp) }">
                    <svg width="8" height="12" viewBox="0 0 8 12" fill-rule="evenodd">
                        <path d="M0 10.6L4.7 6 0 1.4 1.4 0l6.1 6-6.1 6z"></path>
                    </svg>
                </span>
            </header>
            <div class="SharpDate__innerContainer">
                <div class="SharpDate__rContainer">
                    <div class="SharpDate__weekdays">
                        <span class="cell day-header" v-for="d in daysOfWeek">{{ d }}</span>
                    </div>
                    <div class="SharpDate__days">
                        <div class="SharpDate__dayContainer">
                            <span class="cell day blank" v-for="d in blankDays"></span>
                            <span class="cell day"
                                     v-for="day in days"
                                     v-bind:class="dayClasses(day)"
                                     @click="selectDate(day)">{{ day.date }}
                            </span>
                            <span class="cell day blank" v-for="d in remainingDays"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Month View -->
        <div class="SharpDate__calendar" v-show="showMonthView" :class="{open:showMonthView}" v-bind:style="calendarStyle">
            <header>

                <span
                        @click="previousYear"
                        class="prev"
                        v-bind:class="{ 'disabled' : previousYearDisabled(pageTimestamp) }">
                    <svg width="8" height="12" viewBox="0 0 8 12" fill-rule="evenodd">
                        <path d="M7.5 10.6L2.8 6l4.7-4.6L6.1 0 0 6l6.1 6z"></path>
                    </svg>
                </span>
                <span @click="showYearCalendar" class="up">
                    <span class="SharpDate__cur-year">{{ getPageYear() }}</span>
                </span>
                <span
                        @click="nextYear"
                        class="next"
                        v-bind:class="{ 'disabled' : nextYearDisabled(pageTimestamp) }">
                    <svg width="8" height="12" viewBox="0 0 8 12" fill-rule="evenodd">
                        <path d="M0 10.6L4.7 6 0 1.4 1.4 0l6.1 6-6.1 6z"></path>
                    </svg>
                </span>
            </header>
            <div class="SharpDate__innerContainer">
                <div class="SharpDate__rContainer">
                    <div class="SharpDate__monthContainer">
                        <span class="cell month"
                              v-for="month in months"
                              v-bind:class="{ 'selected': month.isSelected, 'disabled': month.isDisabled }"
                              @click.stop="selectMonth(month)">{{ month.month }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Year View -->
        <div class="SharpDate__calendar" v-show="showYearView" :class="{open:showYearView}" v-bind:style="calendarStyle">
            <header>
                <span @click="previousDecade" class="prev"
                      v-bind:class="{ 'disabled' : previousDecadeDisabled(pageTimestamp) }">
                    <svg width="8" height="12" viewBox="0 0 8 12" fill-rule="evenodd">
                        <path d="M7.5 10.6L2.8 6l4.7-4.6L6.1 0 0 6l6.1 6z"></path>
                    </svg>
                </span>
                <span class="up">
                    <span class="SharpDate__cur-decade">{{ getPageDecade() }}</span>
                </span>
                <span @click="nextDecade" class="next"
                      v-bind:class="{ 'disabled' : nextMonthDisabled(pageTimestamp) }">
                    <svg width="8" height="12" viewBox="0 0 8 12" fill-rule="evenodd">
                        <path d="M0 10.6L4.7 6 0 1.4 1.4 0l6.1 6-6.1 6z"></path>
                    </svg>
                </span>
            </header>
            <div class="SharpDate__innerContainer">
                <div class="SharpDate__rContainer">
                    <div class="SharpDate__yearContainer">
                    <span
                            class="cell year"
                            v-for="year in years"
                            v-bind:class="{ 'selected': year.isSelected, 'disabled': year.isDisabled }"
                            @click.stop="selectYear(year)">{{ year.year }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
    import DatePicker from 'vuejs-datepicker';
    export default {
        name:'SharpDatepicker',
        extends:DatePicker,

        computed: {
            remainingDays() {
                let diff = this.days.length + this.blankDays;
                let rem = diff > 35 ? 42 - diff : 35 - diff;
                return rem;
            },
        },

        methods: {
            init() {
                if (this.value) {
                    this.setValue(this.value)
                }
                this.showDayCalendar();
            },
            clickOutside() {

            }
        },
    }
</script>